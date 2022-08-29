<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class ItemsModel extends Model {
	protected $table = 'inventov2_items';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'name',
		'code',
		'code_type',
		'brand_id',
		'sale_price',
		'sale_tax',
		'purchase_price',
		'description',
		'weight',
		'height',
		'depth',
		'min_alert',
		'max_alert',
		'notes',
		'suppliers',
		'created_by',
		'created_at',
		'updated_at',
		'category_id',
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

	// To get all items -- Adapted to DataTables
	public function dtGetAllItems() {
		$recordsTotal = $this
			->select('inventov2_items.*')
			->countAllResults();

		$items = $this
			->select('inventov2_items.id AS DT_RowId,
								inventov2_items.name AS name,
								inventov2_items.code AS code,
								_brand.name AS brand_name,
								_category.name AS category_name,
								inventov2_items.sale_price AS sale_price')
			->groupStart()
			->orLike('inventov2_items.name', $this->dtSearch)
			->orLike('inventov2_items.code', $this->dtSearch)
			->orLike('_brand.name', $this->dtSearch)
			->orLike('_category.name', $this->dtSearch)
			->groupEnd()
			->join('inventov2_brands AS _brand', '_brand.id = inventov2_items.brand_id', 'left')
			->join('inventov2_categories AS _category', '_category.id = inventov2_items.category_id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_items.id');

		$recordsFiltered = $items->countAllResults(false);
		$data = $items->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all items
	public function getDetailedList() {
		$items = $this
			->select('inventov2_items.*,
								_brand.name AS brand_name,
								_category.name AS category_name,
								_user.username AS created_by_username,
								_user.name AS created_by_name')
			->join('inventov2_brands AS _brand', '_brand.id = inventov2_items.brand_id', 'left')
			->join('inventov2_categories AS _category', '_category.id = inventov2_items.category_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_items.created_by', 'left')
			->orderBy('inventov2_items.id', 'ASC')
			->find();

		if(!$items)
			return [];
		
		return $items;
	}
	
	// To get a single item by ID
	public function getItem($id) {
		$item = $this
			->select('inventov2_items.id,
								inventov2_items.name,
								inventov2_items.code,
								inventov2_items.code_type,
								inventov2_items.sale_price,
								inventov2_items.sale_tax,
								inventov2_items.description,
								inventov2_items.weight,
								inventov2_items.width,
								inventov2_items.height,
								inventov2_items.depth,
								inventov2_items.min_alert,
								inventov2_items.max_alert,
								inventov2_items.notes,
								inventov2_items.created_at,
								inventov2_items.updated_at,
								_brand.id AS brand_id,
								_brand.name AS brand_name,
								_category.id AS category_id,
								_category.name AS category_name,
								inventov2_items.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_brands AS _brand', '_brand.id = inventov2_items.brand_id', 'left')
			->join('inventov2_categories AS _category', '_category.id = inventov2_items.category_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_items.created_by', 'left')
			->where('inventov2_items.id', $id)
			->first();

		if(!$item)
			return false;

		$grouper = new JsonGrouper(['brand', 'category', 'created_by'], $item);

		return $grouper->group();
	}

	// To get a single item by code
	public function getItemByCode($code) {
		$item = $this
			->select('inventov2_items.id,
								inventov2_items.name,
								inventov2_items.code,
								inventov2_items.code_type,
								inventov2_items.sale_price,
								inventov2_items.sale_tax,
								inventov2_items.description,
								inventov2_items.weight,
								inventov2_items.width,
								inventov2_items.height,
								inventov2_items.depth,
								inventov2_items.min_alert,
								inventov2_items.max_alert,
								inventov2_items.notes,
								inventov2_items.created_at,
								inventov2_items.updated_at,
								_brand.id AS brand_id,
								_brand.name AS brand_name,
								_category.id AS category_id,
								_category.name AS category_name,
								inventov2_items.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_brands AS _brand', '_brand.id = inventov2_items.brand_id', 'left')
			->join('inventov2_categories AS _category', '_category.id = inventov2_items.category_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_items.created_by', 'left')
			->where('inventov2_items.code', $code)
			->first();

	if(!$item)
		return false;

	$grouper = new JsonGrouper(['brand', 'category', 'created_by'], $item);

	return $grouper->group();
	}

	// To get a single item by id, with information of specific warehouse
	public function getItemWithWarehouse($itemId, $warehouseId) {
		$item = $this
			->select('inventov2_items.id,
								inventov2_items.name,
								inventov2_items.code,
								inventov2_items.code_type,
								inventov2_items.sale_price,
								inventov2_items.sale_tax,
								inventov2_items.description,
								inventov2_items.weight,
								inventov2_items.width,
								inventov2_items.height,
								inventov2_items.depth,
								inventov2_items.min_alert,
								inventov2_items.max_alert,
								inventov2_items.notes,
								inventov2_items.created_at,
								inventov2_items.updated_at,
								_brand.id AS brand_id,
								_brand.name AS brand_name,
								_category.id AS category_id,
								_category.name AS category_name,
								inventov2_items.created_by AS created_by_id,
								_user.name AS created_by_name,
								_quantities.quantity AS quantity')
			->join('inventov2_brands AS _brand', '_brand.id = inventov2_items.brand_id', 'left')
			->join('inventov2_categories AS _category', '_category.id = inventov2_items.category_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_items.created_by', 'left')
			->join('inventov2_quantities AS _quantities', "_quantities.item_id = inventov2_items.id AND _quantities.warehouse_id = {$warehouseId}", 'left')
			->where('inventov2_items.id', $itemId)
			->groupBy('inventov2_items.id')
			->first();

		if(!$item)
			return false;

		$grouper = new JsonGrouper(['brand', 'category', 'created_by'], $item);

		return $grouper->group();
	}

	// To get a single item by code, with information of specific warehouse
	public function getItemByCodeWithWarehouse($itemCode, $warehouseId) {
		$item = $this
			->select('inventov2_items.id,
								inventov2_items.name,
								inventov2_items.code,
								inventov2_items.code_type,
								inventov2_items.sale_price,
								inventov2_items.sale_tax,
								inventov2_items.description,
								inventov2_items.weight,
								inventov2_items.width,
								inventov2_items.height,
								inventov2_items.depth,
								inventov2_items.min_alert,
								inventov2_items.max_alert,
								inventov2_items.notes,
								inventov2_items.created_at,
								inventov2_items.updated_at,
								_brand.id AS brand_id,
								_brand.name AS brand_name,
								_category.id AS category_id,
								_category.name AS category_name,
								inventov2_items.created_by AS created_by_id,
								_user.name AS created_by_name,
								_quantities.quantity AS quantity')
			->join('inventov2_brands AS _brand', '_brand.id = inventov2_items.brand_id', 'left')
			->join('inventov2_categories AS _category', '_category.id = inventov2_items.category_id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_items.created_by', 'left')
			->join('inventov2_quantities AS _quantities', "_quantities.item_id = inventov2_items.id AND _quantities.warehouse_id = {$warehouseId}", 'left')
			->where('inventov2_items.code', $itemCode)
			->groupBy('inventov2_items.id')
			->first();

		if(!$item)
			return false;

		$grouper = new JsonGrouper(['brand', 'category', 'created_by'], $item);

		return $grouper->group();
	}

	// To remove a brand from all items
	public function removeBrandFromAll($brandId) {
		return $this->set('brand_id', null)->where('brand_id', $brandId)->update();
	}

	// To remove a category from all items
	public function removeCategoryFromAll($categoryId) {
		return $this->set('category_id', null)->where('category_id', $categoryId)->update();
	}

	// To get all item IDs
	public function getItemIds() {
		$ids = $this->findColumn('id');

		if(!$ids)
			return [];
			
		return $ids;
	}

	// To get code type of an item
	public function getCodeType($itemId) {
		return $this->select('code_type')->where('id', $itemId)->first()->code_type;
	}

	// To get stats for value in stock
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function statValueInStock(bool $limitByWarehouses = false, array $warehouseIds = []) {
		// Proxy cross join for non-limited queries
		$proxy_join = '(SELECT item_id, SUM(quantity) AS total_qty FROM inventov2_quantities GROUP BY item_id) AS _quantities';

		// Modify it if we need to limit by warehouse IDs
		if($limitByWarehouses) {
			$proxy_join = '(SELECT item_id, warehouse_id, SUM(quantity) AS total_qty FROM inventov2_quantities ';

			if(count($warehouseIds) == 0)
				$proxy_join .= 'WHERE 1=0 GROUP BY item_id) AS _quantities';
			else{
				$wheres = [];
				foreach($warehouseIds as $warehouseId)
					$wheres[] = "warehouse_id = {$warehouseId}";
				$wheres = implode(' OR ', $wheres);
				$proxy_join .= "WHERE {$wheres} GROUP BY item_id) AS _quantities";
			}
		}

		// Now build our query
		$value_in_stock = $this
			->select('SUM(inventov2_items.sale_price * _quantities.total_qty) AS value')
			->join($proxy_join, '_quantities.item_id = inventov2_items.id', 'left')
			->first()
			->value;

		return !$value_in_stock ? 0 : $value_in_stock;
	}

	// To get a list of items (id, name, code), primarily to be displayed in a select
	// Search is allowed
	public function getItemsList($search) {
		$items = $this
			->select('id, name, code')
			->groupStart()
			->orLike('id', $search)
			->orLike('name', $search)
			->orLike('code', $search)
			->groupEnd()
			->find();

		if(!$items)
			return [];

		return $items;
	}

	// To get a list of items (id, name, code), primarily to be displayed in a select
	// We'll limit them by supplier ID
	// Search is allowed
	public function getItemsListLimitedBySupplier($search, $supplierId) {
		$items = $this
			->select('inventov2_items.id,
								inventov2_items.name,
								inventov2_items.code')
			->join('inventov2_item_suppliers AS _supplier', "_supplier.item_id = inventov2_items.id AND _supplier.supplier_id = $supplierId", 'inner')
			->groupStart()
			->orLike('inventov2_items.id', $search)
			->orLike('inventov2_items.name', $search)
			->orLike('inventov2_items.code', $search)
			->groupEnd()
			->groupBy('inventov2_items.id')
			->find();

		if(!$items)
			return [];

		return $items;
	}
}