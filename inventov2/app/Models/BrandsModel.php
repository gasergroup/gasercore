<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class BrandsModel extends Model {
	protected $table = 'inventov2_brands';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'name',
		'created_by',
		'created_at',
		'updated_at',
		'description'
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

	// To get all brands -- Adapted to DataTables
	public function dtGetAllBrands() {
		// First, count all results without filtering -- But include required
		// conditions
		$recordsTotal = $this
			->select('inventov2_brands.*')
			->countAllResults();

		// Now make our actual query
		$brands = $this
			->select('inventov2_brands.id AS DT_RowId,
								inventov2_brands.id AS id,
								inventov2_brands.name AS name,
								_user.name AS created_by_name,
								inventov2_brands.created_at AS created_at,
								COUNT(_items.id) AS items')
			->orLike('inventov2_brands.id', $this->dtSearch)
			->orLike('inventov2_brands.name', $this->dtSearch)
			->orLike('_user.name', $this->dtSearch)
			->orLike('inventov2_brands.description', $this->dtSearch)
			->join('inventov2_users AS _user', '_user.id = inventov2_brands.created_by', 'left')
			->join('inventov2_items AS _items', '_items.brand_id = inventov2_brands.id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_brands.id');

		// Count filtered results (without limit clause), and then get the data
		// False to avoid resetting previously made query
		$recordsFiltered = $brands->countAllResults(false);
		$data = $brands->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all brands
	public function getDetailedList() {
		$brands = $this
			->select('inventov2_brands.id,
								inventov2_brands.name,
								inventov2_brands.created_by,
								_user.username AS created_by_username,
								_user.name AS created_by_name,
								inventov2_brands.created_at,
								inventov2_brands.updated_at,
								COUNT(_items.id) AS items_registered')
			->join('inventov2_items AS _items', '_items.brand_id = inventov2_brands.id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_brands.created_by', 'left')
			->orderBy('inventov2_brands.id', 'ASC')
			->find();

		if(!$brands)
			return [];
		
		return $brands;
	}

	// To get a single brand by ID
	public function getBrand($id) {
		$brand = $this
			->select('inventov2_brands.id,
								inventov2_brands.name,
								inventov2_brands.description,
								inventov2_brands.created_at,
								inventov2_brands.updated_at,
								inventov2_brands.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_brands.created_by', 'left')
			->where('inventov2_brands.id', $id)
			->first();

		if(!$brand)
			return false;

		$grouper = new JsonGrouper('created_by', $brand);

		return $grouper->group();
	}

	// To get a single brand by Name
	public function getBrandByName($name) {
		$brand = $this
			->select('inventov2_brands.id,
								inventov2_brands.name,
								inventov2_brands.description,
								inventov2_brands.created_at,
								inventov2_brands.updated_at,
								inventov2_brands.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_brands.created_by', 'left')
			->where('inventov2_brands.name', $name)
			->first();

		if(!$brand)
			return false;

		$grouper = new JsonGrouper('created_by', $brand);

		return $grouper->group();
	}

	// To get a list of brands (id and name), primarily to be displayed in a select
	public function getBrandsList() {
		$items = $this->select('id, name')->find();

		if(!$items)
			return [];

		return $items;
	}
}