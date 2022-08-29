<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class PurchasesModel extends Model {
	protected $table = 'inventov2_purchases';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'reference',
		'supplier_id',
		'warehouse_id',
		'items',
		'n_items',
		'shipping_cost',
		'discount',
		'discount_type',
		'tax',
		'subtotal',
		'grand_total',
		'created_by',
		'created_at',
		'confirmed_at',
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

	// To get all purchases -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetAllPurchases(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_purchases.*');

		// Should we limit by warehouses? (If user is worker/supervisor)
		if($limitByWarehouses)
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, 'inventov2_purchases.warehouse_id', $warehouseIds);

		$recordsTotal = $recordsTotal->countAllResults();

		$purchases = $this
			->select('inventov2_purchases.id AS DT_RowId,
								inventov2_purchases.reference,
								_warehouse.name AS warehouse_name,
								inventov2_purchases.created_at,
								_supplier.name AS supplier_name,
								inventov2_purchases.grand_total')
			->groupStart()
			->orLike('inventov2_purchases.reference', $this->dtSearch)
			->orLike('_warehouse.name', $this->dtSearch)
			->orLike('_supplier.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_purchases.warehouse_id', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_purchases.supplier_id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_purchases.id');

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$purchases = $this->restrictQueryByIds($purchases, 'inventov2_purchases.warehouse_id', $warehouseIds);

		$recordsFiltered = $purchases->countAllResults(false);
		$data = $purchases->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all purchases
	public function getDetailedList() {
		$purchases = $this
			->select('inventov2_purchases.id,
								inventov2_purchases.reference,
								inventov2_purchases.supplier_id,
								inventov2_purchases.warehouse_id,
								inventov2_purchases.shipping_cost,
								inventov2_purchases.discount,
								inventov2_purchases.discount_type,
								inventov2_purchases.tax,
								inventov2_purchases.subtotal,
								inventov2_purchases.grand_total,
								inventov2_purchases.created_by,
								inventov2_purchases.created_at,
								inventov2_purchases.updated_at,
								inventov2_purchases.notes,
								_supplier.name AS supplier_name,
								_warehouse.name AS warehouse_name,
								_user.username AS created_by_username,
								_user.name AS created_by_name')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_purchases.supplier_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_purchases.warehouse_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_purchases.created_by', 'left')
			->orderBy('inventov2_purchases.id', 'ASC')
			->find();

		if(!$purchases)
			return [];
		
		return $purchases;
	}

	// To get 5 most recent purchases -- Without DataTables features
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetLatest(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$data = $this
			->select('inventov2_purchases.id AS DT_RowId,
								inventov2_purchases.created_at,
								inventov2_purchases.reference,
								_supplier.name AS supplier_name,
								inventov2_purchases.grand_total')
			->join('inventov2_purchases_returns AS _return', '_return.purchase_id = inventov2_purchases.id', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_purchases.supplier_id', 'left')
			->groupBy('inventov2_purchases.id')
			->orderBy('inventov2_purchases.created_at', 'DESC')
			->limit(5);

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$data = $this->restrictQueryByIds($data, 'inventov2_purchases.warehouse_id', $warehouseIds);

		$recordsFiltered = $data->countAllResults(false);
		$recordsTotal = $recordsFiltered;
		$data = $data->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a single purchase by ID
	public function getPurchase($id) {
		$purchase = $this
			->select('inventov2_purchases.id,
								inventov2_purchases.reference,
								inventov2_purchases.items,
								inventov2_purchases.shipping_cost,
								inventov2_purchases.discount,
								inventov2_purchases.discount_type,
								inventov2_purchases.tax,
								inventov2_purchases.subtotal,
								inventov2_purchases.grand_total,
								inventov2_purchases.created_at,
								inventov2_purchases.updated_at,
								inventov2_purchases.notes,
								inventov2_purchases.created_by AS created_by_id,
								_user.name AS created_by_name,
								_supplier.id AS supplier_id,
								_supplier.name AS supplier_name,
								_supplier.address AS supplier_address,
								_supplier.city AS supplier_city,
								_supplier.state AS supplier_state,
								_supplier.zip_code AS supplier_zip_code,
								_supplier.country AS supplier_country,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_return.id AS return_id')
			->join('inventov2_users AS _user', '_user.id = inventov2_purchases.created_by', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_purchases.supplier_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_purchases.warehouse_id', 'left')
			->join('inventov2_purchases_returns AS _return', '_return.purchase_id = inventov2_purchases.id', 'left')
			->where('inventov2_purchases.id', $id)
			->groupBy('inventov2_purchases.id')
			->first();

		if(!$purchase)
			return false;

		$grouper = new JsonGrouper(['created_by', 'supplier', 'warehouse'], $purchase);
		$grouped = $grouper->group();

		$grouped->items = json_decode($grouped->items);

		return $grouped;
	}

	public function getPurchaseByReference($reference) {
		$purchase = $this
			->select('inventov2_purchases.id,
								inventov2_purchases.reference,
								inventov2_purchases.items,
								inventov2_purchases.shipping_cost,
								inventov2_purchases.discount,
								inventov2_purchases.discount_type,
								inventov2_purchases.tax,
								inventov2_purchases.subtotal,
								inventov2_purchases.grand_total,
								inventov2_purchases.created_at,
								inventov2_purchases.updated_at,
								inventov2_purchases.created_by AS created_by_id,
								_user.name AS created_by_name,
								_supplier.id AS supplier_id,
								_supplier.name AS supplier_name,
								_supplier.address AS supplier_address,
								_supplier.city AS supplier_city,
								_supplier.state AS supplier_state,
								_supplier.zip_code AS supplier_zip_code,
								_supplier.country AS supplier_country,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_return.id AS return_id')
			->join('inventov2_users AS _user', '_user.id = inventov2_purchases.created_by', 'left')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_purchases.supplier_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_purchases.warehouse_id', 'left')
			->join('inventov2_purchases_returns AS _return', '_return.purchase_Id = inventov2_purchases.id', 'left')
			->where('inventov2_purchases.reference', $reference)
			->groupBy('inventov2_purchases.id')
			->first();

		if(!$purchase)
			return false;

		$grouper = new JsonGrouper(['created_by', 'supplier', 'warehouse'], $purchase);
		$grouped = $grouper->group();

		$grouped->items = json_decode($grouped->items);

		return $grouped;
	}

	// To get stats for purchases
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function statPurchases($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$purchases = $this
			->selectSum('grand_total')
			->where("created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('inventov2_purchases.id');

		if($limitByWarehouses)
			$purchases = $this->restrictQueryByIds($purchases, 'inventov2_purchases.warehouse_id', $warehouseIds);

		$purchases = $purchases->first();

		return (!$purchases) ? 0 : $purchases->grand_total;
	}

	// To get stats for purchases, to be graphed, with range (between date A and date B)
	public function statPurchasesForGraphWithRange($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$purchases = $this
			->select("SUM(inventov2_purchases.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_purchases.created_at, '%Y-%m-%d') AS created_at")
			->where("inventov2_purchases.created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('DAY(inventov2_purchases.created_at)');

		if($limitByWarehouses)
			$purchases = $this->restrictQueryByIds($purchases, 'inventov2_purchases.warehouse_id', $warehouseIds);

		$purchases = $purchases->find();

		return (!$purchases) ? [] : $purchases;
	}

	// To get stats for purchases, to be graphed, with year
	public function statPurchasesForGraphWithYear($year, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$purchases = $this
			->select("SUM(inventov2_purchases.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_purchases.created_at, '%Y-%m') AS created_at")
			->where("YEAR(inventov2_purchases.created_at) = '{$year}'")
			->groupBy('MONTH(inventov2_purchases.created_at)');

		if($limitByWarehouses)
			$purchases = $this->restrictQueryByIds($purchases, 'inventov2_purchases.warehouse_id', $warehouseIds);

		$purchases = $purchases->find();

		return (!$purchases) ? [] : $purchases;
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