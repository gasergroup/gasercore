<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class AlertsModel extends Model {
	protected $table = 'inventov2_alerts';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'item_id',
		'warehouse_id',
		'type',
		'alert_qty',
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

	// To get all alerts -- Adapted to DataTables
	public function dtGetAllAlerts() {
		$recordsTotal = $this->select('inventov2_alerts.*')->countAllResults();

		$alerts = $this
			->select('inventov2_alerts.item_id AS DT_RowId,
								_item.name AS item_name,
								_warehouse.name AS warehouse_name,
								inventov2_alerts.type,
								IF(inventov2_alerts.type = "min", _item.min_alert, _item.max_alert) AS alert_qty,
								_quantity.quantity AS current_qty,
								inventov2_alerts.created_at')
			->groupStart()
			->orLike('_item.name', $this->dtSearch)
			->orLike('_warehouse.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_items AS _item', '_item.id = inventov2_alerts.item_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_alerts.warehouse_id', 'left')
			->join('inventov2_quantities AS _quantity', '_quantity.item_id = inventov2_alerts.item_id AND _quantity.warehouse_id = inventov2_alerts.warehouse_id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_alerts.id');

		$recordsFiltered = $alerts->countAllResults(false);
		$data = $alerts->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	public function deleteAlertsForItem($itemId) {
		return $this->where('item_id', $itemId)->delete();
	}

	public function triggerMinAlert($itemId, $warehouseId, $minMaxQty) {
		return $this->insert([
			'item_id' => $itemId,
			'warehouse_id' => $warehouseId,
			'type' => 'min',
			'alert_qty' => $minMaxQty
		]);
	}

	public function triggerMaxAlert($itemId, $warehouseId, $minMaxQty) {
		return $this->insert([
			'item_id' => $itemId,
			'warehouse_id' => $warehouseId,
			'type' => 'max',
			'alert_qty' => $minMaxQty
		]);
	}

	// Get latest alerts, to be shown in the header
	public function getLatestAlertsForHeader() {
		$alerts = $this
			->select('inventov2_alerts.item_id,
								inventov2_alerts.warehouse_id,
								inventov2_alerts.type,
								inventov2_alerts.alert_qty,
								inventov2_alerts.created_at,
								_item.name AS item_name,
								_item.min_alert AS item_min_alert,
								_item.max_alert AS item_max_alert,
								_warehouse.name AS warehouse_name,
								_quantity.quantity AS current_qty')
			->join('inventov2_items AS _item', '_item.id = inventov2_alerts.item_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_alerts.warehouse_id', 'left')
			->join('inventov2_quantities AS _quantity', '_quantity.item_id = inventov2_alerts.item_id AND _quantity.warehouse_id = inventov2_alerts.warehouse_id', 'left')
			->groupBy('inventov2_alerts.id')
			->limit(6)
			->find();

		if(!$alerts)
			return [];

		$grouper = new JsonGrouper(['item', 'warehouse'], $alerts);
		
		return $grouper->group();
	}
}