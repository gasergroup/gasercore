<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class SalesReturnsModel extends Model {
	protected $table = 'inventov2_sales_returns';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'reference',
		'sale_id',
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

	// To get all sale returns -- Adapted to DataTables
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehosue IDs provided in $warehouseIds)
	public function dtGetAllReturns(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$recordsTotal = $this->select('inventov2_sales_returns.*');

		// Should we limit by warehouses? (If user is worker/supervisor)
		if($limitByWarehouses) {
			$recordsTotal = $recordsTotal
				->join('inventov2_sales AS _sale', '_sale.id = inventov2_sales_returns.sale_id', 'left');
			$recordsTotal = $this->restrictQueryByIds($recordsTotal, '_sale.warehouse_id', $warehouseIds);
		}

		$recordsTotal = $recordsTotal->countAllResults();

		$returns = $this
			->select('inventov2_sales_returns.id AS DT_RowId,
								inventov2_sales_returns.reference,
								_sale.reference AS sale_reference,
								_warehouse.name AS warehouse_name,
								inventov2_sales_returns.created_at,
								_customer.name AS customer_name,
								inventov2_sales_returns.grand_total')
			->groupStart()
			->orLike('inventov2_sales_returns.reference', $this->dtSearch)
			->orLike('_sale.reference', $this->dtSearch)
			->orLike('_warehouse.name', $this->dtSearch)
			->orLike('_customer.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_sales AS _sale', '_sale.id = inventov2_sales_returns.sale_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _sale.warehouse_id', 'left')
			->join('inventov2_customers AS _customer', '_customer.id = _sale.customer_id')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_sales_returns.id');

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$returns = $this->restrictQueryByIds($returns, '_sale.warehouse_id', $warehouseIds);

		$recordsFiltered = $returns->countAllResults(false);
		$data = $returns->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all sale returns
	public function getDetailedList() {
		$returns = $this
			->select('inventov2_sales_returns.id,
								inventov2_sales_returns.reference,
								inventov2_sales_returns.sale_id,
								inventov2_sales_returns.shipping_cost,
								inventov2_sales_returns.discount,
								inventov2_sales_returns.tax,
								inventov2_sales_returns.subtotal,
								inventov2_sales_returns.grand_total,
								inventov2_sales_returns.created_by,
								inventov2_sales_returns.created_at,
								inventov2_sales_returns.updated_at,
								inventov2_sales_returns.notes,
								_customer.id AS customer_id,
								_customer.name AS customer_name,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name,
								_user.username AS created_by_username,
								_user.name AS created_by_name')
			->join('inventov2_sales AS _sale', '_sale.id = inventov2_sales_returns.sale_id', 'left')
			->join('inventov2_customers AS _customer', '_customer.id = _sale.customer_id', 'left')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _sale.warehouse_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_sales_returns.created_by', 'left')
			->orderBy('inventov2_sales_returns.id', 'ASC')
			->find();

		if(!$returns)
			return [];
		
		return $returns;
	}

	public function getReturn($returnId) {
		$return = $this
		->select('inventov2_sales_returns.id,
							inventov2_sales_returns.reference,
							inventov2_sales_returns.sale_id,
							inventov2_sales_returns.items AS return_items,
							inventov2_sales_returns.shipping_cost,
							inventov2_sales_returns.discount,
							inventov2_sales_returns.tax,
							inventov2_sales_returns.subtotal,
							inventov2_sales_returns.grand_total,
							inventov2_sales_returns.created_at,
							inventov2_sales_returns.updated_at,
							inventov2_sales_returns.updated_at,
							inventov2_sales_returns.notes,
							_warehouse.id AS warehouse_id,
							_warehouse.name AS warehouse_name,
							_customer.id AS customer_id,
							_customer.name AS customer_name,
							_customer.address AS customer_address,
							_customer.city AS customer_city,
							_customer.state AS customer_state,
							_customer.zip_code AS customer_zip_code,
							_customer.country AS customer_country,
							_user.id AS created_by_id,
							_user.name AS created_by_name,
							_sale.id AS sale_id,
							_sale.reference AS sale_reference,
							_sale.items AS sale_items')
		->join('inventov2_sales AS _sale', '_sale.id = inventov2_sales_returns.sale_id', 'left')
		->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _sale.warehouse_id', 'left')
		->join('inventov2_customers AS _customer', '_customer.id = _sale.customer_id', 'left')
		->join('inventov2_users AS _user', '_user.id = inventov2_sales_returns.created_by', 'left')
		->where('inventov2_sales_returns.id', $returnId)
		->first();

		if(!$return)
			return false;

		$grouper = new JsonGrouper(['warehouse', 'customer', 'created_by', 'sale'], $return);
		$grouped = $grouper->group();

		$sale_items = json_decode($grouped->sale->items);
		$return_items = json_decode($grouped->return_items);

		$items = [];
		
		foreach($sale_items AS $sale_item) {
			$newItem = $sale_item;

			foreach($return_items AS $return_item) {
				if($return_item->id == $sale_item->id)
					$newItem->qty_to_return = $return_item->qty_to_return;
			}

			$items[] = $newItem;
		}

		unset($grouped->sale->items);
		unset($grouped->return_items);
		$grouped->items = $items;

		return $grouped;
	}

	public function getSaleReturn($saleId) {
		$return = $this
		->select('inventov2_sales_returns.id,
							inventov2_sales_returns.reference,
							inventov2_sales_returns.sale_id,
							inventov2_sales_returns.items AS return_items,
							inventov2_sales_returns.shipping_cost,
							inventov2_sales_returns.discount,
							inventov2_sales_returns.tax,
							inventov2_sales_returns.subtotal,
							inventov2_sales_returns.grand_total,
							inventov2_sales_returns.created_at,
							inventov2_sales_returns.updated_at,
							inventov2_sales_returns.updated_at,
							inventov2_sales_returns.notes,
							_warehouse.id AS warehouse_id,
							_warehouse.name AS warehouse_name,
							_customer.id AS customer_id,
							_customer.name AS customer_name,
							_customer.address AS customer_address,
							_customer.city AS customer_city,
							_customer.state AS customer_state,
							_customer.zip_code AS customer_zip_code,
							_customer.country AS customer_country,
							_user.id AS created_by_id,
							_user.name AS created_by_name,
							_sale.id AS sale_id,
							_sale.reference AS sale_reference,
							_sale.items AS sale_items')
		->join('inventov2_sales AS _sale', '_sale.id = inventov2_sales_returns.sale_id', 'left')
		->join('inventov2_warehouses AS _warehouse', '_warehouse.id = _sale.warehouse_id', 'left')
		->join('inventov2_customers AS _customer', '_customer.id = _sale.customer_id', 'left')
		->join('inventov2_users AS _user', '_user.id = inventov2_sales_returns.created_by', 'left')
		->where('_sale.id', $saleId)
		->first();

		if(!$return)
			return false;

		$grouper = new JsonGrouper(['warehouse', 'customer', 'created_by', 'sale'], $return);
		$grouped = $grouper->group();

		$sale_items = json_decode($grouped->sale->items);
		$return_items = json_decode($grouped->return_items);

		$items = [];
		
		foreach($sale_items AS $sale_item) {
			$newItem = $sale_item;

			foreach($return_items AS $return_item) {
				if($return_item->id == $sale_item->id)
					$newItem->qty_to_return = $return_item->qty_to_return;
			}

			$items[] = $newItem;
		}

		unset($grouped->sale->items);
		unset($grouped->return_items);
		$grouped->items = $items;

		return $grouped;
	}

	public function getReturnByReference($reference) {
		return $this->where('reference', $reference)->first();
	}

	// To get stats for sales returns
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function statSalesReturns($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$returns = $this
			->selectSum('inventov2_sales_returns.grand_total')
			->where("inventov2_sales_returns.created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('inventov2_sales_returns.id');

		if($limitByWarehouses) {
			$returns = $returns
				->join('inventov2_sales AS _sales', '_sales.id = inventov2_sales_returns.sale_id', 'left');
			$returns = $this->restrictQueryByIds($returns, '_sales.warehouse_id', $warehouseIds);
		}

		$returns = $returns->first();

		return (!$returns) ? 0 : $returns->grand_total;
	}

	// To get stats for sales returns, to be graphed, with range (between date A and date B)
	public function statSalesReturnsForGraphWithRange($fromDate, $toDate, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$returns = $this
			->select("SUM(inventov2_sales_returns.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_sales_returns.created_at, '%Y-%m-%d') AS created_at")
			->where("inventov2_sales_returns.created_at BETWEEN '{$fromDate} 00:00:00' AND '{$toDate} 23:59:59'")
			->groupBy('DAY(inventov2_sales_returns.created_at)');

		if($limitByWarehouses) {
			$returns = $returns->join('inventov2_sales AS _sales', '_sales.id = inventov2_sales_returns.sale_id', 'left');
			$returns = $this->restrictQueryByIds($returns, '_sales.warehouse_id', $warehouseIds);
		}

		$returns = $returns->find();

		return (!$returns) ? [] : $returns;
	}

	// To get stats for sales returns, to be graphed, with year
	public function statSalesReturnsForGraphWithYear($year, bool $limitByWarehouses = false, array $warehouseIds = []) {
		$returns = $this
			->select("SUM(inventov2_sales_returns.grand_total) AS grand_total,
								DATE_FORMAT(inventov2_sales_returns.created_at, '%Y-%m') AS created_at")
			->where("YEAR(inventov2_sales_returns.created_at) = '{$year}'")
			->groupBy('MONTH(inventov2_sales_returns.created_at)');

		if($limitByWarehouses) {
			$returns = $returns->join('inventov2_sales AS _sales', '_sales.id = inventov2_sales_returns', 'left');
			$returns = $this->restrictQueryByIds($returns, '_sales.warehouse_id', $warehouseIds);
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