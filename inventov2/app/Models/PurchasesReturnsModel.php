<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class PurchasesReturnsModel extends Model {
	protected $table = 'inventov2_purchases_returns';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'reference',
		'purchase_id',
		'items',
		'shipping_cost',
		'discount',
		'tax',
		'subtotal',
		'grand_total',
		'created_by',
		'created_at',
		'updated_at',
		'notes',
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

	// To get all purchase returns -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehosue IDs provided in $warehouseIds)
	public function dtGetAllReturns(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_purchases_returns.*');

		// Should we limit by warehouses? (If user is worker/supervisor)
		if($limitByWarehouses) {
			$recordsTotal = $recordsTotal
				->join('inventov2_purchases AS _purchase', '_purchase.id = inventov2_purchases_returns.purchase_id', 'left');
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, '_purchase.warehouse_id', $warehouseIds);
		}

		$recordsTotal = $recordsTotal->countAllResults();

		$returns = $this
			->select('inventov2_purchases_returns.id AS DT_RowId,
								inventov2_purchases_returns.reference,
								_purchase.reference AS purchase_reference,
								_warehouse.name AS warehouse_name,
								inventov2_purchases_returns.created_at,
								_supplier.name AS supplier_name,
								inventov2_purchases_returns.grand_total')
			->groupStart()
			->orLike('inventov2_purchases_returns.reference', $this->dtSearch)
			->orLike('_purchase.reference', $this->dtSearch)
			->orLike('_warehouse.name', $this->dtSearch)
			->orLike('_supplier.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_purchases AS _purchase', '_purchase.id = inventov2_purchases_returns.purchase_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _purchase.warehouse_id', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = _purchase.supplier_id')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_purchases_returns.id');

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$returns = $this->restrictQueryByIds($returns, '_purchase.warehouse_id', $warehouseIds);

		$recordsFiltered = $returns->countAllResults(false);
		$data = $returns->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all purchase returns
	public function getDetailedList() {
		$returns = $this
			->select('inventov2_purchases_returns.id,
								inventov2_purchases_returns.reference,
								inventov2_purchases_returns.purchase_id,
								inventov2_purchases_returns.shipping_cost,
								inventov2_purchases_returns.discount,
								inventov2_purchases_returns.tax,
								inventov2_purchases_returns.subtotal,
								inventov2_purchases_returns.grand_total,
								inventov2_purchases_returns.created_by,
								inventov2_purchases_returns.created_at,
								inventov2_purchases_returns.updated_at,
								inventov2_purchases_returns.notes,
								_supplier.id AS supplier_id,
								_supplier.name AS supplier_name,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_user.username AS created_by_username,
								_user.name AS created_by_name')
			->join('inventov2_purchases AS _purchase', '_purchase.id = inventov2_purchases_returns.purchase_id', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = _purchase.supplier_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _purchase.warehouse_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_purchases_returns.created_by', 'left')
			->orderBy('inventov2_purchases_returns.id', 'ASC')
			->find();

		if(!$returns)
			return [];
		
		return $returns;
	}

	public function getReturn($returnId) {
		$return = $this
			->select('inventov2_purchases_returns.id,
								inventov2_purchases_returns.reference,
								inventov2_purchases_returns.purchase_id,
								inventov2_purchases_returns.items AS return_items,
								inventov2_purchases_returns.shipping_cost,
								inventov2_purchases_returns.discount,
								inventov2_purchases_returns.tax,
								inventov2_purchases_returns.subtotal,
								inventov2_purchases_returns.grand_total,
								inventov2_purchases_returns.created_at,
								inventov2_purchases_returns.updated_at,
								inventov2_purchases_returns.updated_at,
								inventov2_purchases_returns.notes,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_supplier.id AS supplier_id,
								_supplier.name AS supplier_name,
								_supplier.address AS supplier_address,
								_supplier.city AS supplier_city,
								_supplier.state AS supplier_state,
								_supplier.zip_code AS supplier_zip_code,
								_supplier.country AS supplier_country,
								_user.id AS created_by_id,
								_user.name AS created_by_name,
								_purchase.id AS purchase_id,
								_purchase.reference AS purchase_reference,
								_purchase.items AS purchase_items')
			->join('inventov2_purchases AS _purchase', '_purchase.id = inventov2_purchases_returns.purchase_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _purchase.warehouse_id', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = _purchase.supplier_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_purchases_returns.created_by', 'left')
			->where('inventov2_purchases_returns.id', $returnId)
			->first();

		if(!$return)
			return false;

		$grouper = new JsonGrouper(['warehouse', 'supplier', 'created_by', 'purchase'], $return);
		$grouped = $grouper->group();

		$purchase_items = json_decode($grouped->purchase->items);
		$return_items = json_decode($grouped->return_items);

		$items = [];
		
		foreach($purchase_items AS $purchase_item) {
			$newItem = $purchase_item;

			foreach($return_items AS $return_item) {
				if($return_item->id == $purchase_item->id)
					$newItem->qty_to_return = $return_item->qty_to_return;
			}

			$items[] = $newItem;
		}

		unset($grouped->purchase->items);
		unset($grouped->return_items);
		$grouped->items = $items;

		return $grouped;
	}

	public function getPurchaseReturn($purchaseId) {
		$return = $this
		->select('inventov2_purchases_returns.id,
							inventov2_purchases_returns.reference,
							inventov2_purchases_returns.purchase_id,
							inventov2_purchases_returns.items AS return_items,
							inventov2_purchases_returns.shipping_cost,
							inventov2_purchases_returns.discount,
							inventov2_purchases_returns.tax,
							inventov2_purchases_returns.subtotal,
							inventov2_purchases_returns.grand_total,
							inventov2_purchases_returns.created_at,
							inventov2_purchases_returns.updated_at,
							inventov2_purchases_returns.updated_at,
							inventov2_purchases_returns.notes,
							_warehouse.id AS warehouse_id,
							_warehouse.name AS warehouse_name,
							_supplier.id AS supplier_id,
							_supplier.name AS supplier_name,
							_supplier.address AS supplier_address,
							_supplier.city AS supplier_city,
							_supplier.state AS supplier_state,
							_supplier.zip_code AS supplier_zip_code,
							_supplier.country AS supplier_country,
							_user.id AS created_by_id,
							_user.name AS created_by_name,
							_purchase.id AS purchase_id,
							_purchase.reference AS purchase_reference,
							_purchase.items AS purchase_items')
		->join('inventov2_purchases AS _purchase', '_purchase.id = inventov2_purchases_returns.purchase_id', 'left')
		->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _purchase.warehouse_id', 'left')
		->join('inventov2_suppliers AS _supplier', '_supplier.id = _purchase.supplier_id', 'left')
		->join('inventov2_users AS _user', '_user.id = inventov2_purchases_returns.created_by', 'left')
		->where('_purchase.id', $purchaseId)
		->first();

		if(!$return)
			return false;

		$grouper = new JsonGrouper(['warehouse', 'supplier', 'created_by', 'purchase'], $return);
		$grouped = $grouper->group();

		$purchase_items = json_decode($grouped->purchase->items);
		$return_items = json_decode($grouped->return_items);

		$items = [];
		
		foreach($purchase_items AS $purchase_item) {
			$newItem = $purchase_item;

			foreach($return_items AS $return_item) {
				if($return_item->id == $purchase_item->id)
					$newItem->qty_to_return = $return_item->qty_to_return;
			}

			$items[] = $newItem;
		}

		unset($grouped->purchase->items);
		unset($grouped->return_items);
		$grouped->items = $items;

		return $grouped;
	}

	public function getReturnByReference($reference) {
		return $this->where('reference', $reference)->first();
	}

	// To get stats for purchases returns
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function statPurchasesReturns($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$returns = $this
			->selectSum('inventov2_purchases_returns.grand_total')
			->where("inventov2_purchases_returns.created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('inventov2_purchases_returns.id');

		if($limitByWarehouses) {
			$returns = $returns
				->join('inventov2_purchases AS _purchases', '_purchases.id = inventov2_purchases_returns.purchase_id', 'left');
			$returns = $this->restrictQueryByIds($returns, '_purchases.warehouse_id', $warehouseIds);
		}

		$returns = $returns->first();

		return (!$returns) ? 0 : $returns->grand_total;
	}

	// To get stats for purchases returns, to be graphed, with range (between date A and date B)
	public function statPurchasesReturnsForGraphWithRange($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$returns = $this
			->select("SUM(inventov2_purchases_returns.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_purchases_returns.created_at, '%Y-%m-%d') AS created_at")
			->where("inventov2_purchases_returns.created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('DAY(inventov2_purchases_returns.created_at)');

		if($limitByWarehouses) {
			$returns = $returns->join('inventov2_purchases AS _purchases', '_purchases.id = inventov2_purchases_returns.purchase_id', 'left');
			$returns = $this->restrictQueryByIds($returns, '_purchases.warehouse_id', $warehouseIds);
		}

		$returns = $returns->find();

		return (!$returns) ? [] : $returns;
	}

	// To get stats for purchases returns, to be graphed, with year
	public function statPurchasesReturnsForGraphWithYear($year, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$returns = $this
			->select("SUM(inventov2_purchases_returns.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_purchases_returns.created_at, '%Y-%m') AS created_at")
			->where("YEAR(inventov2_purchases_returns.created_at) = '{$year}'")
			->groupBy('MONTH(inventov2_purchases_returns.created_at)');

		if($limitByWarehouses) {
			$returns = $returns->join('inventov2_purchases AS _purchases', '_purchases.id = inventov2_purchases_returns', 'left');
			$returns = $this->restrictQueryByIds($returns, '_purchases.warehouse_id', $warehouseIds);
		}

		$returns = $returns->find();

		return (!$returns) ? [] : $returns;
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