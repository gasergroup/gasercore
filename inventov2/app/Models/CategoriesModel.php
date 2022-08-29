<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class CategoriesModel extends Model {
	protected $table = 'inventov2_categories';
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

	// To get all categories -- Adapted to DataTables
	public function dtGetAllCategories() {
		// First, count all results without filtering -- But include required
		// conditions
		$recordsTotal = $this
			->select('inventov2_categories.*')
			->countAllResults();

		// Now make our actual query
		$categories = $this
			->select('inventov2_categories.id AS DT_RowId,
								inventov2_categories.id AS id,
								inventov2_categories.name AS name,
								_user.name AS created_by_name,
								inventov2_categories.created_at AS created_at,
								COUNT(_items.id) AS items')
			->orLike('inventov2_categories.id', $this->dtSearch)
			->orLike('inventov2_categories.name', $this->dtSearch)
			->orLike('_user.name', $this->dtSearch)
			->orLike('inventov2_categories.description', $this->dtSearch)
			->join('inventov2_users AS _user', '_user.id = inventov2_categories.created_by', 'left')
			->join('inventov2_items AS _items', '_items.category_id = inventov2_categories.id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_categories.id');

		// Count filtered results (without limit clause), and then get the data
		// False to avoid resetting previously made query
		$recordsFiltered = $categories->countAllResults(false);
		$data = $categories->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all categories
	public function getDetailedList() {
		$categories = $this
			->select('inventov2_categories.id,
								inventov2_categories.name,
								inventov2_categories.created_by,
								_user.username AS created_by_username,
								_user.name AS created_by_name,
								inventov2_categories.created_at,
								inventov2_categories.updated_at,
								COUNT(_items.id) AS items_registered')
			->join('inventov2_items AS _items', '_items.category_id = inventov2_categories.id', 'left')
			->join('inventov2_users AS _user', '_user.id = inventov2_categories.created_by', 'left')
			->orderBy('inventov2_categories.id', 'ASC')
			->find();

		if(!$categories)
			return [];
		
		return $categories;
	}

	// To get a single category by ID
	public function getCategory($id) {
		$category = $this
			->select('inventov2_categories.id,
								inventov2_categories.name,
								inventov2_categories.description,
								inventov2_categories.created_at,
								inventov2_categories.updated_at,
								inventov2_categories.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_categories.created_by', 'left')
			->where('inventov2_categories.id', $id)
			->first();

		if(!$category)
			return false;

		$grouper = new JsonGrouper('created_by', $category);

		return $grouper->group();
	}

	// To get a single category by Name
	public function getCategoryByName($name) {
		$category = $this
			->select('inventov2_categories.id,
								inventov2_categories.name,
								inventov2_categories.description,
								inventov2_categories.created_at,
								inventov2_categories.updated_at,
								inventov2_categories.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_categories.created_by', 'left')
			->where('inventov2_categories.name', $name)
			->first();

		if(!$category)
			return false;

		$grouper = new JsonGrouper('created_by', $category);

		return $grouper->group();
	}

	// To get a list of categories (id and name), primarily to be displayed in a select
	public function getCategoriesList() {
		$items = $this->select('id, name')->find();

		if(!$items)
			return [];

		return $items;
	}
}