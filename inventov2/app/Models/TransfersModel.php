<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class TransfersModel extends Model {
	protected $table = 'inventov2_transfers';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'from_warehouse_id',
		'to_warehouse_id',
		'items',
		'notes',
		'created_by',
		'created_at',
		'updated_at'
	];

	protected $useTimestamps = true;
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';

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

	// To get all transfers -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds.. This check will only
	// apply to from_warehouse_id)
	// This is so that if a supervisor gets a quantity transfer FROM his warehouse ID,
	// he'll know about it
	public function dtGetAllTransfers(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_transfers.*');

		// Should we limit by warehouses? (If user is supervisor)
		if($limitByWarehouses)
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, 'inventov2_transfers.from_warehouse_id', $warehouseIds);

		$recordsTotal = $recordsTotal->countAllResults();

		$transfers = $this
			->select('inventov2_transfers.id AS DT_RowId,
								_from_warehouse.name AS from_warehouse_name,
								_to_warehouse.name AS to_warehouse_name,
								_user.name AS created_by,
								inventov2_transfers.created_at')
			->groupStart()
			->orLike('_from_warehouse.name', $this->dtSearch)
			->orLike('_to_warehouse.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_warehouses AS _from_warehouse', '_from_warehouse.id = inventov2_transfers.from_warehouse_id', 'left')
			->join('inventov2_warehouses AS _to_warehouse', '_to_warehouse.id = inventov2_transfers.to_warehouse_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_transfers.created_by', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_transfers.id');

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$transfers = $this->restrictQueryByIds($transfers, 'inventov2_transfers.from_warehouse_id', $warehouseIds);

		$recordsFiltered = $transfers->countAllResults(false);
		$data = $transfers->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a single transfer by ID
	public function getTransfer($id) {
		$transfer = $this
			->select('inventov2_transfers.id,
								inventov2_transfers.items,
								inventov2_transfers.notes,
								inventov2_transfers.created_at,
								_user.id AS created_by_id,
								_user.name AS created_by_name,
								_from_warehouse.id AS from_warehouse_id,
								_from_warehouse.name AS from_warehouse_name,
								_to_warehouse.id AS to_warehouse_id,
								_to_warehouse.name AS to_warehouse_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_transfers.created_by', 'left')
			->join('inventov2_warehouses AS _from_warehouse', '_from_warehouse.id = inventov2_transfers.from_warehouse_id', 'left')
			->join('inventov2_warehouses AS _to_warehouse', '_to_warehouse.id = inventov2_transfers.to_warehouse_id', 'left')
			->where('inventov2_transfers.id', $id)
			->groupBy('inventov2_transfers.id')
			->first();

		if(!$transfer)
			return false;

		$grouper = new JsonGrouper(['created_by', 'from_warehouse', 'to_warehouse'], $transfer);
		$grouped = $grouper->group();

		$grouped->items = json_decode($grouped->items);

		return $grouped;
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