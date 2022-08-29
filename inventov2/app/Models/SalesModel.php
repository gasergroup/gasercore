<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class SalesModel extends Model {
	protected $table = 'inventov2_sales';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'reference',
		'customer_id',
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

	// To get all sales -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetAllSales(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_sales.*');

		// Should we limit by warehouses? (If user is worker/supervisor)
		if($limitByWarehouses)
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, 'inventov2_sales.warehouse_id', $warehouseIds);

		$recordsTotal = $recordsTotal->countAllResults();

		$sales = $this
			->select('inventov2_sales.id AS DT_RowId,
								inventov2_sales.reference,
								_warehouse.name AS warehouse_name,
								inventov2_sales.created_at,
								_customer.name AS customer_name,
								inventov2_sales.grand_total')
			->groupStart()
			->orLike('inventov2_sales.reference', $this->dtSearch)
			->orLike('_warehouse.name', $this->dtSearch)
			->orLike('_customer.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_sales.warehouse_id', 'left')
			->join('inventov2_customers AS _customer', '_customer.id = inventov2_sales.customer_id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_sales.id');

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$sales = $this->restrictQueryByIds($sales, 'inventov2_sales.warehouse_id', $warehouseIds);

		$recordsFiltered = $sales->countAllResults(false);
		$data = $sales->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all sales
	public function getDetailedList() {
		$sales = $this
			->select('inventov2_sales.id,
								inventov2_sales.reference,
								inventov2_sales.customer_id,
								inventov2_sales.warehouse_id,
								inventov2_sales.shipping_cost,
								inventov2_sales.discount,
								inventov2_sales.discount_type,
								inventov2_sales.tax,
								inventov2_sales.subtotal,
								inventov2_sales.grand_total,
								inventov2_sales.created_by,
								inventov2_sales.created_at,
								inventov2_sales.updated_at,
								inventov2_sales.notes,
								_customer.name AS customer_name,
								_warehouse.name AS warehouse_name,
								_user.username AS created_by_username,
								_user.name AS created_by_name')
			->join('inventov2_customers AS _customer', '_customer.id = inventov2_sales.customer_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_sales.warehouse_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_sales.created_by', 'left')
			->orderBy('inventov2_sales.id', 'ASC')
			->find();

		if(!$sales)
			return [];
		
		return $sales;
	}

	// To get 5 most recent sales -- Without DataTables features
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetLatest(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$data = $this
			->select('inventov2_sales.id AS DT_RowId,
								inventov2_sales.created_at,
								inventov2_sales.reference,
								_customer.name AS customer_name,
								inventov2_sales.grand_total')
			->join('inventov2_sales_returns AS _return', '_return.sale_id = inventov2_sales.id', 'left')
			->join('inventov2_customers AS _customer', '_customer.id = inventov2_sales.customer_id', 'left')
			->groupBy('inventov2_sales.id')
			->orderBy('inventov2_sales.created_at', 'DESC')
			->limit(5);

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$data = $this->restrictQueryByIds($data, 'inventov2_sales.warehouse_id', $warehouseIds);

		$recordsFiltered = $data->countAllResults(false);
		$recordsTotal = $recordsFiltered;
		$data = $data->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a single sale by ID
	public function getSale($id) {
		$sale = $this
			->select('inventov2_sales.id,
								inventov2_sales.reference,
								inventov2_sales.items,
								inventov2_sales.shipping_cost,
								inventov2_sales.discount,
								inventov2_sales.discount_type,
								inventov2_sales.tax,
								inventov2_sales.subtotal,
								inventov2_sales.notes,
								inventov2_sales.grand_total,
								inventov2_sales.created_at,
								inventov2_sales.updated_at,
								inventov2_sales.created_by AS created_by_id,
								_user.name AS created_by_name,
								_customer.id AS customer_id,
								_customer.name AS customer_name,
								_customer.address AS customer_address,
								_customer.city AS customer_city,
								_customer.state AS customer_state,
								_customer.zip_code AS customer_zip_code,
								_customer.country AS customer_country,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_return.id AS return_id')
			->join('inventov2_users AS _user', '_user.id = inventov2_sales.created_by', 'left')
			->join('inventov2_customers AS _customer', '_customer.id = inventov2_sales.customer_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_sales.warehouse_id', 'left')
			->join('inventov2_sales_returns AS _return', '_return.sale_id = inventov2_sales.id', 'left')
			->where('inventov2_sales.id', $id)
			->groupBy('inventov2_sales.id')
			->first();

		if(!$sale)
			return false;

		$grouper = new JsonGrouper(['created_by', 'customer', 'warehouse'], $sale);
		$grouped = $grouper->group();

		$grouped->items = json_decode($grouped->items);

		return $grouped;
	}

	public function getSaleByReference($reference) {
		$sale = $this
			->select('inventov2_sales.id,
								inventov2_sales.reference,
								inventov2_sales.items,
								inventov2_sales.shipping_cost,
								inventov2_sales.discount,
								inventov2_sales.discount_type,
								inventov2_sales.tax,
								inventov2_sales.subtotal,
								inventov2_sales.notes,
								inventov2_sales.grand_total,
								inventov2_sales.created_at,
								inventov2_sales.updated_at,
								inventov2_sales.created_by AS created_by_id,
								_user.name AS created_by_name,
								_customer.id AS customer_id,
								_customer.name AS customer_name,
								_customer.address AS customer_address,
								_customer.city AS customer_city,
								_customer.state AS customer_state,
								_customer.zip_code AS customer_zip_code,
								_customer.country AS customer_country,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_return.id AS return_id')
			->join('inventov2_users AS _user', '_user.id = inventov2_sales.created_by', 'left')
			->join('inventov2_customers AS _customer', '_customer.id = inventov2_sales.customer_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_sales.warehouse_id', 'left')
			->join('inventov2_sales_returns AS _return', '_return.sale_id = inventov2_sales.id', 'left')
			->where('inventov2_sales.reference', $reference)
			->groupBy('inventov2_sales.id')
			->first();

		if(!$sale)
			return false;

		$grouper = new JsonGrouper(['created_by', 'customer', 'warehouse'], $sale);
		$grouped = $grouper->group();

		$grouped->items = json_decode($grouped->items);

		return $grouped;
	}

	// To get stats for sales
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function statSales($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$sales = $this
			->selectSum('grand_total')
			->where("created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'");

		if($limitByWarehouses)
			$sales = $this->restrictQueryByIds($sales, 'inventov2_sales.warehouse_id', $warehouseIds);

		$sales = $sales->first();

		return (!$sales) ? 0 : $sales->grand_total;
	}

	// To get stats for sales, to be graphed, with range (between date A and date B)
	public function statSalesForGraphWithRange($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$sales = $this
			->select("SUM(inventov2_sales.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_sales.created_at, '%Y-%m-%d') AS created_at")
			->where("inventov2_sales.created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('DAY(inventov2_sales.created_at)');

		if($limitByWarehouses)
			$sales = $this->restrictQueryByIds($sales, 'inventov2_sales.warehouse_id', $warehouseIds);

		$sales = $sales->find();

		return (!$sales) ? [] : $sales;
	}

	// To get stats for sales, to be graphed, with year
	public function statSalesForGraphWithYear($year, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$sales = $this
			->select("SUM(inventov2_sales.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_sales.created_at, '%Y-%m') AS created_at")
			->where("YEAR(inventov2_sales.created_at) = '{$year}'")
			->groupBy('MONTH(inventov2_sales.created_at)');

		if($limitByWarehouses)
			$sales = $this->restrictQueryByIds($sales, 'inventov2_sales.warehouse_id', $warehouseIds);

		$sales = $sales->find();

		return (!$sales) ? [] : $sales;
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