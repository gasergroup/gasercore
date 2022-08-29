<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Warehouses extends BaseController {

	// Define create and update rules
	private $rules = [
		'create' => [
			'name' => [
				'rules' => 'min_length[1]|max_length[100]',
				'errors' => [
					'min_length' => 'Validation.warehouses.name_min_length',
					'max_length' => 'Validation.warehouses.name_max_length'
				]
			],
			'address' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.warehouses.address_max_length'
				]
			],
			'city' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.warehouses.city_max_length'
				]
			],
			'country' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.warehouses.country_max_length'
				]
			],
			'state' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.warehouses.state_max_length'
				]
			],
			'zip_code' => [
				'rules' => 'permit_empty|integer|max_length[12]',
				'errors' => [
					'integer' => 'Validations.warehouses.zip_code_invalid',
					'max_length' => 'Validation.warehouses.zip_code_max_length'
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					'max_length' => 'Validation.warehouses.phone_number_max_length'
				]
			]
		],

		'update' => [
			'name' => [
				'rules' => 'permit_empty|min_length[1]|max_length[100]',
				'errors' => [
					'min_length' => 'Validation.warehouses.name_min_length',
					'max_length' => 'Validation.warehouses.name_max_length'
				]
			],
			'address' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.warehouses.address_max_length'
				]
			],
			'city' => [
				'rules' => 'permit_empty|max_length[80]',
				'errors' => [
					'max_length' => 'Validation.warehouses.city_max_length'
				]
			],
			'country' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.warehouses.country_max_length'
				]
			],
			'state' => [
				'rules' => 'permit_empty|max_length[30]',
				'errors' => [
					'max_length' => 'Validation.warehouses.state_max_length'
				]
			],
			'zip_code' => [
				'rules' => 'permit_empty|integer|max_length[12]',
				'errors' => [
					'integer' => 'Validations.warehouses.zip_code_invalid',
					'max_length' => 'Validation.warehouses.zip_code_max_length'
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					'max_length' => 'Validation.warehouses.phone_number_max_length'
				]
			]
		]
	];

	public function __construct() {
		$this->rules = (object) $this->rules;
	}

	/**
	 * To get all warehouses
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function index() {
		$columns = [
			'name',
			'address',
			'phone_number',
			'total_quantity',
			'total_value'
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

		$this->warehouses->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->warehouses->dtGetAllWarehouses(true, $warehouseIds);
		}else{
			$result = $this->warehouses->dtGetAllWarehouses();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To get a single warehouse by ID
	 * 
	 * Supervisors will be able to see all users responsible of
	 * this warehouse, but won't be able to modify anything
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show($id) {
		$warehouse = $this->warehouses->getWarehouse($id);

		if(!$warehouse)
			return $this->failNotFound(lang('Errors.warehouses.not_found', ['id' => $id]));

		$warehouse->workers = $this->warehouse_relations->getWorkersResponsibleOfWarehouse($id);
		$warehouse->supervisors = $this->warehouse_relations->getSupervisorsResponsibleOfWarehouse($id);

		return $this->respond($warehouse);
	}

	/**
	 * To create a new warehouse
	 * 
	 * Method			POST
	 * Filter			auth:admin
	 */
	public function create() {
		if(!$this->validateRequestWithRules($this->rules->create))
			return $this->failWithValidationErrors();

		$createFields = [
			'name',
			'address',
			'city',
			'country',
			'state',
			'zip_code',
			'phone_number'
		];

		$data = $this->buildCreateArray($createFields, true);

		if($data['zip_code'] == '')
			$data['zip_code'] = null;

		if($this->warehouses->getWarehouseByName($data['name']))
			return $this->failResourceExists(lang('Errors.warehouses.already_exists_name', ['name' => $data['name']]));

		$data['created_by'] = $this->logged_user->id;

		$warehouse_id = $this->warehouses->insert($data);

		// After inserting warehouse, let's insert quantity records for
		// all existing items
		$itemIds = $this->items->getItemIds();
		foreach($itemIds as $itemId) {
			$newItemQty = [
				'item_id' => $itemId,
				'warehouse_id' => $warehouse_id,
				'quantity' => 0
			];

			$this->quantities->insert($newItemQty);
		}

		$new_warehouse = $this->warehouses->getWarehouse($warehouse_id);

		return $this->respondCreated($new_warehouse);
	}

	/**
	 * To edit a warehouse
	 * 
	 * Method			PUT
	 * Filter			auth:admin
	 */
	public function update($id) {
		if(!$this->validateRequestWithRules($this->rules->update))
			return $this->failWithValidationErrors();

		if(!$this->warehouses->find($id))
			return $this->failNotFound(lang('Errors.warehouses.not_found', ['id' => $id]));

		$updateFields = [
			'name',
			'address',
			'city',
			'country',
			'state',
			'zip_code',
			'phone_number'
		];

		$data = $this->buildUpdateArray($updateFields, true);

		if($data['zip_code'] == '')
			$data['zip_code'] = null;

		// If trying to edit warehouse name, make sure it doesn't
		// exist already
		if(isset($data['name'])) {
			$duplicateWarehouse = $this->warehouses->getWarehouseByName($data['name']);
			if($duplicateWarehouse && $duplicateWarehouse->id != $id)
				return $this->failResourceExists(lang('Errors.warehouses.already_exists_name', ['name' => $data['name']]));
		}

		$this->warehouses->update($id, $data);

		return $this->respondUpdated($this->warehouses->getWarehouse($id));
	}
	
	/**
	 * To delete a warehouse (soft)
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function delete($id) {
		if(!$this->warehouses->find($id))
			return $this->failNotFound(lang('Errors.warehouses.not_found', ['id' => $id]));

		// Make sure there are no quantities left
		if($this->quantities->getWarehouseTotalQty($id) > 0)
			return $this->fail(lang('Errors.warehouses.quantities_left'));

		// Soft delete warehouse
		$this->warehouses->delete($id);

		// After deleting, let's remove it from each and every user that had access to it,
		// including soft-deleted users
		$this->warehouse_relations->deleteWarehouseRelations($id);

		// Also, remove quantity rows
		$this->quantities->deleteWarehouseQuantities($id);

		// We're done
		return $this->respondDeleted(['id' => $id]);
	}

	/**
	 * To get a list of workers not responsible of a warehouse, to
	 * be used in a select
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 */
	public function pending_workers_list($warehouseId) {
		if(!$this->warehouses->find($warehouseId))
			return $this->failNotFound(lang('Errors.warehouses.not_found', ['id' => $warehouseId]));

		return $this->respond($this->users->getWorkersNotResponsibleOfWarehouse($warehouseId));
	}

	/**
	 * To get a list of supervisors not responsible of a warehouse, to
	 * be used in a select
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 */
	public function pending_supervisors_list($warehouseId) {
		if(!$this->warehouses->find($warehouseId))
			return $this->failNotFound(lang('Errors.warehouses.not_found', ['id' => $warehouseId]));
		
		return $this->respond($this->users->getSupervisorsNotResponsibleOfWarehouse($warehouseId));
	}

	/**
	 * To get a list of warehouses a user is responsible of (if worker or supervisor),
	 * if admin we'll return all warehouses
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function list() {
		$warehouses = [];

		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor')
			$warehouses = $this->warehouses->getWarehousesUserHasAccessTo($this->logged_user->id);
		else if($this->logged_user->role == 'admin')
			$warehouses = $this->warehouses->getWarehousesList();
		
		return $this->respond($warehouses);
	}

	/**
	 * To export a CSV file with all warehouses (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of warehouses, with as much information as we can get
		$warehouses = $this->warehouses->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "warehouses__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($warehouses, $filename));
	}
}