<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class WarehousesModel extends Model {
	protected $table = 'inventov2_warehouses';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'name',
		'address',
		'city',
		'country',
		'state',
		'zip_code',
		'phone_number',
		'created_by',
		'created_at',
		'updated_at',
		'deleted_at'
	];

	protected $useTimestamps = true;
	protected $useSoftDeletes = true;
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';
	protected $deletedField = 'deleted_at';

	// DataTables parameters
	private $dtSearch;
	private $dtOrderBy;
	private $dtOrderDir;
	private $dtLength;
	private $dtStart;

	// To load DataTables parameters
	public function setDtParameters($search, $orderBy, $orderDir, $length, $start) {
		$this->dtSearch = $search;
		$this->dtOrderBy = $orderBy;
		$this->dtOrderDir = $orderDir;
		$this->dtLength = $length;
		$this->dtStart = $start;
	}

	// To get all warehouses -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds
	public function dtGetAllWarehouses(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_warehouses.*');

		// Should we limit by warehouses? (If user is worker/supervisor)
		if($limitByWarehouses)
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, 'inventov2_warehouses.id', $warehouseIds);
		
		$recordsTotal = $recordsTotal->countAllResults();

		$warehouses = $this
			->select('inventov2_warehouses.id AS DT_RowId,
								inventov2_warehouses.name AS name,
								inventov2_warehouses.address AS address,
								inventov2_warehouses.phone_number AS phone_number,
								_values.total_qty AS total_qty,
								_values.total_value AS total_value')
			->groupStart()
			->orLike('inventov2_warehouses.name', $this->dtSearch)
			->orLike('inventov2_warehouses.address', $this->dtSearch)
			->orLike('inventov2_warehouses.phone_number', $this->dtSearch)
			->groupEnd()
			->join('(SELECT
								warehouse_id,
								SUM(_quantities.quantity) AS total_qty,
								SUM(_quantities.quantity * _item.sale_price) AS total_value
							FROM
								inventov2_quantities AS _quantities
							INNER JOIN
								inventov2_items AS _item ON _item.id = _quantities.item_id
							GROUP BY warehouse_id) AS _values', '_values.warehouse_id = inventov2_warehouses.id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_warehouses.id');
		
		// Should we limit by warehouse?
		if($limitByWarehouses)
			$warehouses = $this->restrictQueryByIds($warehouses, 'inventov2_warehouses.id', $warehouseIds);
		
		$recordsFiltered = $warehouses->countAllResults(false);
		$data = $warehouses->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all warehouses
	public function getDetailedList() {
		$warehouse = $this
			->select('inventov2_warehouses.id,
								inventov2_warehouses.name,
								inventov2_warehouses.address,
								inventov2_warehouses.phone_number,
								inventov2_warehouses.created_by,
								_user.username AS created_by_username,
								_user.name AS created_by_name,
								inventov2_warehouses.created_at,
								inventov2_warehouses.updated_at,
								_values.total_qty AS total_quantity,
								_values.total_value AS total_sale_value')
			->join('inventov2_users AS _user', '_user.id = inventov2_warehouses.created_by', 'left')
			->join('(SELECT
								warehouse_id,
								SUM(_quantities.quantity) AS total_qty,
								SUM(_quantities.quantity * _item.sale_price) AS total_value
							FROM
								inventov2_quantities AS _quantities
							INNER JOIN
								inventov2_items AS _item ON _item.id = _quantities.item_id
							GROUP BY warehouse_id) AS _values', '_values.warehouse_id = inventov2_warehouses.id', 'left')
			->orderBy('inventov2_warehouses.id', 'ASC')
			->groupBy('inventov2_warehouses.id')
			->find();

		if(!$warehouse)
			return [];
		
		return $warehouse;
	}

	// To get a single warehouse by ID
	public function getWarehouse($id) {
		$warehouse = $this
			->select('inventov2_warehouses.id,
								inventov2_warehouses.name,
								inventov2_warehouses.address,
								inventov2_warehouses.city,
								inventov2_warehouses.country,
								inventov2_warehouses.state,
								inventov2_warehouses.zip_code,
								inventov2_warehouses.phone_number,
								inventov2_warehouses.created_at,
								inventov2_warehouses.updated_at,
								inventov2_warehouses.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_warehouses.created_by', 'left')
			->where('inventov2_warehouses.id', $id)
			->first();
		
		if(!$warehouse)
			return false;

		$grouper = new JsonGrouper('created_by', $warehouse);

		return $grouper->group();
	}

	// To get a single warehouse by name
	public function getWarehouseByName($name) {
		$warehouse = $this
			->select('inventov2_warehouses.id,
								inventov2_warehouses.name,
								inventov2_warehouses.address,
								inventov2_warehouses.city,
								inventov2_warehouses.country,
								inventov2_warehouses.state,
								inventov2_warehouses.zip_code,
								inventov2_warehouses.phone_number,
								inventov2_warehouses.created_at,
								inventov2_warehouses.updated_at,
								inventov2_warehouses.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_warehouses.created_by', 'left')
			->where('inventov2_warehouses.name', $name)
			->first();
		
		if(!$warehouse)
			return false;

		$grouper = new JsonGrouper('created_by', $warehouse);

		return $grouper->group();
	}

	// To get an array of warehouse IDs
	public function getWarehouseIds() {
		$warehouseIds = $this->findColumn('id');

		if(!$warehouseIds)
			return [];

		return $warehouseIds;
	}

	// To get a list of warehouses that a user doesn't have access to
	public function getWarehousesUserIsNotResponsible($userId) {
		$warehouses = $this
			->select('inventov2_warehouses.id AS id,
								inventov2_warehouses.name AS name')
			->join('inventov2_warehouse_relations AS _relation', "_relation.warehouse_id = inventov2_warehouses.id AND _relation.user_id = $userId", 'left')
			->join('inventov2_users AS _user', "_user.id = $userId", 'left')
			->where('_user.deleted_at is null')
			->where('_relation.user_id is null')
			->groupBy('inventov2_warehouses.id')
			->find();

		if(!$warehouses)
			return [];

		return $warehouses;
	}

	// To get a list of all warehouses (id and name)
	public function getWarehousesList() {
		$warehouses = $this
			->select('id, name')
			->find();

		if(!$warehouses)
			return [];

		return $warehouses;
	}

	// To get a list of warehouses that a worker/supervisor has access to (id and name)
	public function getWarehousesUserHasAccessTo($userId) {
		$warehouses = $this
			->select('inventov2_warehouses.id,
								inventov2_warehouses.name')
			->join('inventov2_warehouse_relations AS _relation', "_relation.warehouse_id = inventov2_warehouses.id AND _relation.user_id = $userId", 'inner')
			->groupBy('inventov2_warehouses.id')
			->find();

		if(!$warehouses)
			return [];

		return $warehouses;
	}

	/**
	 * This function will restrict a query, so that $column only has
	 * the values provided in the $ids array
	 */
	private function restrictQueryByIds($query, string $column, array $ids) {
		if(count($ids) == 0)
			$query->where('1=0', null, false);
		else{
			$query->groupStart();
			foreach($ids as $id)
				$query->orWhere($column, $id);
			$query->groupEnd();
		}

		return $query;
	}
}