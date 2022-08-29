<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Categories extends BaseController {

	// Define create and update rules
	private $rules = [
		'create' => [
			'name' => [
				'rules' => 'min_length[1]|max_length[100]',
				'errors' => [
					"min_length" => "Validation.categories.name_min_length",
					"max_length" => "Validation.categories.name_max_length"
				]
			],
			'description' => [
				'rules' => 'permit_empty',
				'errors' => []
			]
		],

		'update' => [
			'name' => [
				'rules' => 'permit_empty|min_length[1]|max_length[100]',
				'errors' => [
					"min_length" => "Validation.categories.name_min_length",
					"max_length" => "Validation.categories.name_max_length"
				]
			],
			'description' => [
				'rules' => 'permit_empty',
				'errors' => []
			]
		]
	];

	public function __construct() {
		$this->rules = (object) $this->rules;
	}

	/**
	 * To get all categories
	 * 
	 * Method			GET
	 * Filter			auth
	 * 
	 */
	public function index() {
		$columns = ['id', 'name', 'created_by_name', 'created_at', 'items'];
		$datatables = new DataTables($this->request, $columns);

		if($datatables->isRequestValid() === false)
			return $this->failUnauthorized(lang('Errors.unauthorized'));
		
		$draw = $datatables->getDraw();
		$length = $datatables->getLength();
		$start = $datatables->getStart();
		$search = $datatables->getSearchStr();
		$orderBy = $datatables->getOrderBy();
		$orderDir = $datatables->getOrderDir();

		if($orderBy === false || $orderDir === false)
			return $this->fail(lang('Errors.invalid_order'));

		$this->categories->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		return $this->respond(array_merge(
			['draw' => $draw],
			$this->categories->dtGetAllCategories()
		));
	}

	/**
	 * To get a single category by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show($id) {
		$category = $this->categories->getCategory($id);

		if(!$category)
			return $this->failNotFound(lang('Errors.categories.not_found', ['id' => $id]));
		
		return $this->respond($category);
	}

	/**
	 * To create a new category
	 * 
	 * Method			POST
	 * Filter			auth
	 */
	public function create() {
		// Run validation according to the rules we've set
		if(!$this->validateRequestWithRules($this->rules->create))
			return $this->failWithValidationErrors();

		// Create data to insert (sanitize HTML)
		$data = $this->buildCreateArray(['name', 'description'], true);
		
		// Make sure category name doesn't exist
		if($this->categories->getCategoryByName($data['name']))
			return $this->failResourceExists(lang('Errors.categories.already_exists', ['name' => $data['name']]));

		// Add extra values we might need
		$data['created_by'] = $this->logged_user->id;

		// Insert and retrieve inserted
		$category_id = $this->categories->insert($data);
		$new_category = $this->categories->getCategory($category_id);

		// Return newly created
		return $this->respondCreated($new_category);
	}

	/**
	 * To edit a category
	 * 
	 * Method			PUT
	 * Filter			auth:supervisor,admin
	 */
	public function update($id) {
		// Run validation according to the rules we've set
		if(!$this->validateRequestWithRules($this->rules->update))
			return $this->failWithValidationErrors();

		// Make sure category exists
		if(!$this->categories->find($id))
			return $this->failNotFound(lang('Errors.categories.not_found', ['id' => $id]));

		// Create data to update (sanitize html)
		$data = $this->buildUpdateArray(['name', 'description'], true);

		// If trying to edit category name, make sure it doesn't exist already
		if(isset($data['name'])) {
			$duplicateCategory = $this->categories->getCategoryByName($data['name']);
			if($duplicateCategory && $duplicateCategory->id != $id)
				return $this->failResourceExists(lang('Errors.categories.already_exists', ['name' => $data['name']]));
		}

		// Update
		$this->categories->update($id, $data);
		
		// Return updated info
		return $this->respondUpdated($this->categories->getCategory($id));
	}

	/**
	 * To delete a category
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function delete($id) {
		// Make sure the category exists
		if(!$this->categories->find($id))
			return $this->failNotFound(lang('Errors.categories.not_found', ['id' => $id]));

		// Remove category from all items
		$this->items->removeCategoryFromAll($id);

		// Delete category
		$this->categories->delete($id);

		// Respond
		return $this->respondDeleted([
			'id' => $id
		]);
	}

	/**
	 * To export a CSV file with all categories (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of categories, with as much information as we can get
		$categories = $this->categories->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "categories__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($categories, $filename));
	}
}