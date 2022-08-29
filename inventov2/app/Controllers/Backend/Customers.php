<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Customers extends BaseController {

	// Define create and update rules
	private $rules = [
		'create' => [
			'name' => [
				'rules' => 'min_length[1]|max_length[100]',
				'errors' => [
					"min_length" => "Validation.customers.name_min_length",
					"max_length" => "Validation.customers.name_max_length"
				]
			],
			'internal_name' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					"max_length" => "Validation.customers.internal_name_max_length"
				]
			],
			'company_name' => [
				'rules' => 'permit_empty|max_length[100]',
				'errors' => [
					"max_length" => "Validation.customers.company_name_max_length"
				]
			],
			'tax_number' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					"max_length" => "Validation.customers.tax_number_max_length"
				]
			],
			'email_address' => [
				'rules' => 'permit_empty|valid_email|max_length[255]',
				'errors' => [
					"valid_email" => "Validation.customers.email_address_invalid",
					"max_length" => "Validation.customers.email_address_max_length"
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					"max_length" => "Validation.customers.phone_number_max_length"
				]
			],
			'address' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					"max_length" => "Validation.customers.address_max_length"
				]
			],
			'country' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					"max_length" => "Validation.customers.country_max_length"
				]
			],
			'state' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					"max_length" => "Validation.customers.state_max_length"
				]
			],
			'zip_code' => [
				'rules' => 'permit_empty|is_natural_no_zero|max_length[12]',
				'errors' => [
					"is_natural_no_zero" => "Validation.customers.zip_code_invalid",
					"max_length" => "Validation.customers.zip_code_max_length"
				]
			],
			'custom_field1' => [
				'rules' => 'permit_empty'
			],
			'custom_field2' => [
				'rules' => 'permit_empty'
			],
			'custom_field3' => [
				'rules' => 'permit_empty'
			],
			'notes' => [
				'rules' => 'permit_empty'
			]
		],

		'update' => [
			'name' => [
				'rules' => 'permit_empty|min_length[1]|max_length[100]',
				'errors' => [
					"max_length" => "Validation.customers.name_max_length"
				]
			],
			'internal_name' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					"max_length" => "Validation.customers.internal_name_max_length"
				]
			],
			'company_name' => [
				'rules' => 'permit_empty|max_length[100]',
				'errors' => [
					"max_length" => "Validation.customers.company_name_max_length"
				]
			],
			'tax_number' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					"max_length" => "Validation.customers.tax_number_max_length"
				]
			],
			'email_address' => [
				'rules' => 'permit_empty|valid_email|max_length[255]',
				'errors' => [
					"valid_email" => "Validation.customers.email_address_invalid",
					"max_length" => "Validation.customers.email_address_max_length"
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					"max_length" => "Validation.customers.phone_number_max_length"
				]
			],
			'address' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					"max_length" => "Validation.customers.address_max_length"
				]
			],
			'country' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					"max_length" => "Validation.customers.country_max_length"
				]
			],
			'state' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					"max_length" => "Validation.customers.state_max_length"
				]
			],
			'zip_code' => [
				'rules' => 'permit_empty|is_natural_no_zero|max_length[12]',
				'errors' => [
					"is_natural_no_zero" => "Validation.customers.zip_code_invalid",
					"max_length" => "Validation.customers.zip_code_max_length"
				]
			],
			'custom_field1' => [
				'rules' => 'permit_empty'
			],
			'custom_field2' => [
				'rules' => 'permit_empty'
			],
			'custom_field3' => [
				'rules' => 'permit_empty'
			],
			'notes' => [
				'rules' => 'permit_empty'
			]
		]
	];

	public function __construct() {
		$this->rules = (object) $this->rules;
	}

	/**
	 * To get all customers
	 * 
	 * Method			GET
	 * Filter			auth
	 * 
	 */
	public function index() {
		$columns = [
			'name',
			'internal_name',
			'company_name',
			'email_address',
			'phone_number',
			'tax_number'
		];
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

		$this->customers->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		return $this->respond(array_merge(
			['draw' => $draw],
			$this->customers->dtGetAllCustomers()
		));
	}

	/**
	 * To get a single customer by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show($id) {
		$customer = $this->customers->getCustomer($id);

		if(!$customer)
			return $this->failNotFound(lang('Errors.customers.not_found', ['id' => $id]));

		return $this->respond($customer);
	}

	/**
	 * To create a new customer
	 * 
	 * Method			POST
	 * Filter			auth
	 */
	public function create() {
		if(!$this->validateRequestWithRules($this->rules->create))
			return $this->failWithValidationErrors();

		$createFields = [
			'name',
			'internal_name',
			'company_name',
			'tax_number',
			'email_address',
			'phone_number',
			'address',
			'country',
			'state',
			'zip_code',
			'custom_field1',
			'custom_field2',
			'custom_field3',
			'notes'
		];

		$data = $this->buildCreateArray($createFields, true);

		if($data['zip_code'] == '')
			$data['zip_code'] = null;

		if($this->customers->getCustomerByName($data['name']))
			return $this->failResourceExists(lang('Errors.customers.already_exists_name', ['name' => $data['name']]));
		
		if($this->customers->getCustomerByInternalName($data['internal_name']))
			return $this->failResourceExists(lang('Errors.customers.already_exists_internal_name', ['internal_name' => $data['internal_name']]));

		$data['created_by'] = $this->logged_user->id;

		$customer_id = $this->customers->insert($data);
		$new_customer = $this->customers->getCustomer($customer_id);

		return $this->respondCreated($new_customer);
	}

	/**
	 * To edit a customer
	 * 
	 * Method			PUT
	 * Filter			auth:supervisor,admin
	 */
	public function update($id) {
		if(!$this->validateRequestWithRules($this->rules->update))
			return $this->failWithValidationErrors();

		if(!$this->customers->find($id))
			return $this->failNotFound(lang('Errors.customers.not_found', ['id' => $id]));
			
		$updateFields = [
			'name',
			'internal_name',
			'company_name',
			'tax_number',
			'email_address',
			'phone_number',
			'address',
			'country',
			'state',
			'zip_code',
			'custom_field1',
			'custom_field2',
			'custom_field3',
			'notes'
		];

		$data = $this->buildUpdateArray($updateFields, true);

		if($data['zip_code'] == '')
			$data['zip_code'] = null;

		// If trying to edit customer name or internal name, make sure they
		// don't exist already
		if(isset($data['name'])) {
			$duplicateCustomer = $this->customers->getCustomerByName($data['name']);
			if($duplicateCustomer && $duplicateCustomer->id != $id)
				return $this->failResourceExists(lang('Errors.customers.already_exists_name', ['name' => $data['name']]));
		}

		if(isset($data['internal_name'])) {
			$duplicateCustomer = $this->customers->getCustomerByInternalName($data['internal_name']);
			if($duplicateCustomer && $duplicateCustomer->id != $id)
				return $this->failResourceExists(lang('Errors.customers.already_exists_internal_name', ['internal_name' => $data['internal_name']]));
		}

		$this->customers->update($id, $data);

		return $this->respondUpdated($this->customers->getCustomer($id));
	}

	/**
	 * To get latest table -- Table with the 5 most recent customers
	 * No DataTables features will be allowed
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show_latest_table() {
		// If user is supervisor, get only records from warehouses that the supervisor has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->customers->dtGetLatest(true, $warehouseIds);
		}else{
			$result = $this->customers->dtGetLatest();
		}

		$draw = $this->request->getVar('draw') ?? false;

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To delete a customer
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function delete($id) {
		if(!$this->customers->find($id))
			return $this->failNotFound(lang('Errors.customers.not_found', ['id' => $id]));
			
		$this->customers->delete($id);

		return $this->respondDeleted([
			'id' => $id
		]);
	}

	/**
	 * To get a list of customers, to be used in a select
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function list() {
		return $this->respond($this->customers->getCustomersList());
	}

	/**
	 * To export a CSV file with all customers (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of customers, with as much information as we can get
		$customers = $this->customers->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "customers__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($customers, $filename));
	}
}