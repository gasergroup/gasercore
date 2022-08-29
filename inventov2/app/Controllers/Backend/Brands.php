<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Brands extends BaseController {

	// Define create and update rules
	private $rules = [
		'create' => [
			'name' => [
				'rules' => 'min_length[1]|max_length[100]',
				'errors' => [
					"min_length" => "Validation.brands.name_min_length",
					"max_length" => "Validation.brands.name_max_length"
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
					"min_length" => "Validation.brands.name_min_length",
					"max_length" => "Validation.brands.name_max_length"
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
	 * To get all brands
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

		$this->brands->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		return $this->respond(array_merge(
			['draw' => $draw],
			$this->brands->dtGetAllBrands()
		));
	}

	/**
	 * To get a single brand by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show($id) {
		$brand = $this->brands->getBrand($id);

		if(!$brand)
			return $this->failNotFound(lang('Errors.brands.not_found', ['id' => $id]));
		
		return $this->respond($brand);
	}

	/**
	 * To create a new brand
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
		
		// Make sure brand name doesn't exist
		if($this->brands->getBrandByName($data['name']))
			return $this->failResourceExists(lang('Errors.brands.already_exists', ['name' => $data['name']]));

		// Add extra values we might need
		$data['created_by'] = $this->logged_user->id;

		// Insert and retrieve inserted
		$brand_id = $this->brands->insert($data);
		$new_brand = $this->brands->getBrand($brand_id);

		// Return newly created
		return $this->respondCreated($new_brand);
	}

	/**
	 * To edit a brand
	 * 
	 * Method			PUT
	 * Filter			auth:supervisor,admin
	 */
	public function update($id) {
		// Run validation according to the rules we've set
		if(!$this->validateRequestWithRules($this->rules->update))
			return $this->failWithValidationErrors();

		// Make sure brand exists
		if(!$this->brands->find($id))
			return $this->failNotFound(lang('Errors.brands.not_found', ['id' => $id]));

		// Create data to update (sanitize html)
		$data = $this->buildUpdateArray(['name', 'description'], true);

		// If trying to edit brand name, make sure it doesn't exist already
		if(isset($data['name'])) {
			$duplicateBrand = $this->brands->getBrandByName($data['name']);
			if($duplicateBrand && $duplicateBrand->id != $id)
				return $this->failResourceExists(lang('Errors.brands.already_exists', ['name' => $data['name']]));
		}

		// Update
		$this->brands->update($id, $data);
		
		// Return updated info
		return $this->respondUpdated($this->brands->getBrand($id));
	}

	/**
	 * To delete a brand
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function delete($id) {
		// Make sure the brand exists
		if(!$this->brands->find($id))
			return $this->failNotFound(lang('Errors.brands.not_found', ['id' => $id]));

		// Remove brand from all items
		$this->items->removeBrandFromAll($id);

		// Delete brand
		$this->brands->delete($id);

		// Respond
		return $this->respondDeleted([
			'id' => $id
		]);
	}

	/**
	 * To export a CSV file with all brands (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of categories, with as much information as we can get
		$brands = $this->brands->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "brands__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($brands, $filename));
	}
}