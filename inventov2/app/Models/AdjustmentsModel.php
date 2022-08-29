<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class AdjustmentsModel extends Model {
	protected $table = 'inventov2_adjustments';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'warehouse_id',
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

	// To get all quantity adjustments -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetAllAdjustments(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_adjustments.*');

		// Should we limit by warehouses? (If user is supervisor)
		if($limitByWarehouses)
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, 'inventov2_adjustments.warehouse_id', $warehouseIds);

		$recordsTotal = $recordsTotal->countAllResults();

		$adjustments = $this
			->select('inventov2_adjustments.id AS DT_RowId,
								_warehouse.name AS warehouse_name,
								_user.name AS created_by,
								inventov2_adjustments.created_at')
			->groupStart()
			->orLike('_warehouse.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_adjustments.warehouse_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_adjustments.created_by', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_adjustments.id');

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$adjustments = $this->restrictQueryByIds($adjustments, 'inventov2_adjustments.warehouse_id', $warehouseIds);

		$recordsFiltered = $adjustments->countAllResults(false);
		$data = $adjustments->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a single quantity adjustment by ID
	public function getAdjustment($id) {
		$adjustment = $this
			->select('inventov2_adjustments.id,
								inventov2_adjustments.items,
								inventov2_adjustments.notes,
								inventov2_adjustments.created_at,
								_user.id AS created_by_id,
								_user.name AS created_by_name,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_adjustments.created_by', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_adjustments.warehouse_id', 'left')
			->where('inventov2_adjustments.id', $id)
			->groupBy('inventov2_adjustments.id')
			->first();

		if(!$adjustment)
			return false;

		$grouper = new JsonGrouper(['created_by', 'warehouse'], $adjustment);
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