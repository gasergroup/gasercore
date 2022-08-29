<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Suppliers extends BaseController {

	// Define create and update rules
	private $rules = [
		'create' => [
			'name' => [
				'rules' => 'min_length[1]|max_length[100]',
				'errors' => [
					'min_length' => 'Validation.suppliers.name_min_length',
					'max_length' => 'Validation.suppliers.name_max_length'
				]
			],
			'internal_name' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					'max_length' => 'Validation.suppliers.internal_name_max_length'
				]
			],
			'company_name' => [
				'rules' => 'permit_empty|max_length[100]',
				'errors' => [
					'max_length' => 'Validation.suppliers.company_name_max_legnth'
				]
			],
			'vat' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					'max_length' => 'Validation.suppliers.vat_max_length'
				]
			],
			'email_address' => [
				'rules' => 'permit_empty|valid_email|max_length[255]',
				'errors' => [
					'valid_email' => 'Validation.suppliers.email_address_invalid',
					'max_length' => 'Validation.suppliers.email_address_max_length'
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					'max_length' => 'Validation.suppliers.phone_number_max_length'
				]
			],
			'address' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.suppliers.address_max_length'
				]
			],
			'city' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.suppliers.city_max_length'
				]
			],
			'country' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.suppliers.country_max_length'
				]
			],
			'state' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.suppliers.state_max_length'
				]
			],
			'zip_code' => [
				'rules' => 'permit_empty|integer|max_length[12]',
				'errors' => [
					'integer' => 'Validation.suppliers.zip_code_invalid',
					'max_length' => 'Validation.suppliers.zip_code_max_length'
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
					'min_length' => 'Validation.suppliers.name_min_length',
					'max_length' => 'Validation.suppliers.name_max_length'
				]
			],
			'internal_name' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					'max_length' => 'Validation.suppliers.internal_name_max_length'
				]
			],
			'company_name' => [
				'rules' => 'permit_empty|max_length[100]',
				'errors' => [
					'max_length' => 'Validation.suppliers.company_name_max_legnth'
				]
			],
			'vat' => [
				'rules' => 'permit_empty|max_length[45]',
				'errors' => [
					'max_length' => 'Validation.suppliers.vat_max_length'
				]
			],
			'email_address' => [
				'rules' => 'permit_empty|valid_email|max_length[255]',
				'errors' => [
					'valid_email' => 'Validation.suppliers.email_address_invalid',
					'max_length' => 'Validation.suppliers.email_address_max_length'
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					'max_length' => 'Validation.suppliers.phone_number_max_length'
				]
			],
			'address' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.suppliers.address_max_length'
				]
			],
			'city' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.suppliers.city_max_length'
				]
			],
			'country' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.suppliers.country_max_length'
				]
			],
			'state' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.suppliers.state_max_length'
				]
			],
			'zip_code' => [
				'rules' => 'permit_empty|integer|max_length[12]',
				'errors' => [
					'integer' => 'Validation.suppliers.zip_code_invalid',
					'max_length' => 'Validation.suppliers.zip_code_max_length'
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
	 * To get all suppliers
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function index() {
		$columns = [
			'name',
			'internal_name',
			'company_name',
			'email_address',
			'phone_number',
			'vat_number'
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

		$this->suppliers->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		return $this->respond(array_merge(
			['draw' => $draw],
			$this->suppliers->dtGetAllSuppliers()
		));
	}

	/**
	 * To get a single supplier by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show($id) {
		$supplier = $this->suppliers->getSupplier($id);

		if(!$supplier)
			return $this->failNotFound(lang('Errors.suppliers.not_found', ['id' => $id]));

		return $this->respond($supplier);
	}

	/**
	 * To create a new supplier
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function create() {
		if(!$this->validateRequestWithRules($this->rules->create))
			return $this->failWithValidationErrors();

		$createFields = [
			'name',
			'internal_name',
			'company_name',
			'vat',
			'email_address',
			'phone_number',
			'address',
			'city',
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

		if($this->suppliers->getSupplierByname($data['name']))
			return $this->failResourceExists(lang('Errors.suppliers.already_exists_name', ['name' => $data['name']]));
		
		if($this->suppliers->getSupplierByInternalName($data['internal_name']))
			return $this->failResourceExists(lang('Errors.suppliers.already_exists_internal_name', ['internal_name' => $data['internal_name']]));

		$data['created_by'] = $this->logged_user->id;

		$supplier_id = $this->suppliers->insert($data);
		$new_supplier = $this->suppliers->getSupplier($supplier_id);

		return $this->respondCreated($new_supplier);
	}

	/**
	 * To edit a supplier
	 * 
	 * Method			PUT
	 * Filter			auth:supervisor,admin
	 */
	public function update($id) {
		if(!$this->validateRequestWithRules($this->rules->update))
			return $this->failWithValidationErrors();

		if(!$this->suppliers->find($id))
			return $this->failNotFound(lang('Errors.suppliers.not_found', ['id' => $id]));

		$updateFields = [
			'name',
			'internal_name',
			'company_name',
			'vat',
			'email_address',
			'phone_number',
			'address',
			'city',
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

		if(isset($data['name'])) {
			$duplicateSupplier = $this->suppliers->getSupplierByname($data['name']);
			if($duplicateSupplier && $duplicateSupplier->id != $id)
				return $this->failResourceExists(lang('Errors.suppliers.already_exists_name', ['name' => $data['name']]));
		}
		
		if(isset($data['internal_name'])) {
			$duplicateSupplier = $this->suppliers->getSupplierByInternalName($data['internal_name']);
			if($duplicateSupplier && $duplicateSupplier->id != $id)
				return $this->failResourceExists(lang('Errors.suppliers.already_exists_internal_name', ['internal_name' => $data['internal_name']]));
		}

		$this->suppliers->update($id, $data);

		return $this->respondUpdated($this->suppliers->getSupplier($id));
	}

	/**
	 * To get latest table -- Table with the 5 most recent suppliers
	 * No DataTables features will be allowed
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show_latest_table() {
		// If user is supervisor, get only records from warehouses that the supervisor has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->suppliers->dtGetLatest(true, $warehouseIds);
		}else{
			$result = $this->suppliers->dtGetLatest();
		}

		$draw = $this->request->getVar('draw') ?? false;

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To delete a supplier
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function delete($id) {
		if(!$this->suppliers->find($id))
			return $this->failNotFound(lang('Errors.suppliers.not_found', ['id' => $id]));

		$this->suppliers->delete($id);

		// Delete supplier-item relations
		$this->item_suppliers->deleteSupplierRelations($id);
		
		return $this->respondDeleted([
			'id' => $id
		]);
	}

	/**
	 * To get a list of suppliers, to be used in a select
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function list() {
		return $this->respond($this->suppliers->getSuppliersList());
	}

	/**
	 * To export a CSV file with all suppliers (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of suppliers, with as much information as we can get
		$suppliers = $this->suppliers->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "suppliers__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($suppliers, $filename));
	}
}