<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Users extends BaseController {

	// Define create and update rules
	private $rules = [
		'create' => [
			'name' => [
				'rules' => 'min_length[2]|max_length[100]',
				'errors' => [
					'min_length' => 'Validation.users.name_min_length',
					'max_length' => 'Validation.users.name_max_length'
				]
			],
			'username' => [
				'rules' => 'min_length[5]|max_length[30]',
				'errors' => [
					'min_length' => 'Validation.users.username_min_length',
					'max_length' => 'Validation.users.username_max_length'
				]
			],
			'password' => [
				'rules' => 'min_length[5]|max_length[30]',
				'errors' => [
					'min_length' => 'Validation.users.password_min_length',
					'max_length' => 'Validation.users.password_max_length'
				]
			],
			'email_address' => [
				'rules' => 'valid_email',
				'errors' => [
					'valid_email' => 'Validation.users.email_address_invalid'
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					'max_length' => 'Validation.users.phone_number_max_length'
				]
			],
			'role' => [
				'rules' => 'in_list[worker,supervisor,admin]',
				'errors' => [
					'in_list' => 'Validation.users.role_invalid'
				]
			]
		],

		'update' => [
			'name' => [
				'rules' => 'permit_empty|min_length[2]|max_length[100]',
				'errors' => [
					'min_length' => 'Validation.users.name_min_length',
					'max_length' => 'Validation.users.name_max_length'
				]
			],
			'username' => [
				'rules' => 'permit_empty|min_length[5]|max_length[30]',
				'errors' => [
					'min_length' => 'Validation.users.username_min_length',
					'max_length' => 'Validation.users.username_max_length'
				]
			],
			'email_address' => [
				'rules' => 'permit_empty|valid_email',
				'errors' => [
					'valid_email' => 'Validation.users.email_address_invalid'
				]
			],
			'phone_number' => [
				'rules' => 'permit_empty|max_length[20]',
				'errors' => [
					'max_length' => 'Validation.users.phone_number_max_length'
				]
			],
			'password' => [
				'rules' => 'permit_empty|min_length[5]|max_length[30]',
				'errors' => [
					'min_length' => 'Validation.users.password_min_length',
					'max_length' => 'Validation.users.password_max_length'
				]
			],
		]
	];

	public function __construct() {
		$this->rules = (object) $this->rules;
	}

	/**
	 * To get all users
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 */
	public function index() {
		$columns = [
			'name',
			'username',
			'email_address',
			'phone_number',
			'role' // access to warehouses not sortable
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

		$this->users->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		return $this->respond(array_merge(
			['draw' => $draw],
			$this->users->dtGetAllUsers()
		));
	}

	/**
	 * To get a single user by ID
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 */
	public function show($id) {
		$user = $this->users->getUser($id);

		if(!$user)
			return $this->failNotFound(lang('Errors.users.not_found', ['id' => $id]));

		$user->warehouses = $this->warehouse_relations->getWarehousesByUser($id) ?: [];

		return $this->respond($user);
	}

	/**
	 * To create a new user
	 * 
	 * Method			POST
	 * Filter			auth:admin
	 */
	public function create() {
		if($this->settings->getSetting('is_demo') == 1)
			return $this->fail(lang('Errors.demo.cannot_create_users'));

		if(!$this->validateRequestWithRules($this->rules->create))
			return $this->failWithValidationErrors();

		$createFields = [
			'name',
			'username',
			'password',
			'email_address',
			'phone_number',
			'role'
		];

		$data = $this->buildCreateArray($createFields, true);

		if($this->users->getUserByUsername($data['username']))
			return $this->failResourceExists(lang('Errors.users.already_exists_username', ['username' => $data['username']]));

		if($this->users->getUserByEmailAddress($data['email_address']))
			return $this->failResourceExists(lang('Errors.users.already_exists_email_address', ['email_address' => $data['email_address']]));

		// Hash password
		$data['password'] = password_hash(
			$data['password'],
			PASSWORD_BCRYPT,
			['cost' => 10]
		);

		$user_id = $this->users->insert($data);
		$new_user = $this->users->getUser($user_id);

		return $this->respondCreated($new_user);
	}

	/**
	 * To edit a user
	 * 
	 * Method			PUT
	 * Filter			auth:admin
	 */
	public function update($id) {
		if($this->settings->getSetting('is_demo') == 1)
			return $this->fail(lang('Errors.demo.cannot_update_users'));

		if(!$this->validateRequestWithRules($this->rules->update))
			return $this->failWithValidationErrors();

		if(!$this->users->find($id))
			return $this->failNotFound(lang('Errors.users.not_found', ['id' => $id]));
			
		$updateFields = [
			'name',
			'username',
			'password',
			'email_address',
			'phone_number'
		];

		$data = $this->buildUpdateArray($updateFields, true);

		if(isset($data['password']) && ($data['password'] == '' || $data['password'] == null))
			unset($data['password']);

		// If trying to edit username or email_address, make sure they don't
		// exist already
		if(isset($data['username'])) {
			$duplicateUsername = $this->users->getUserByUsername($data['username']);
			if($duplicateUsername && $duplicateUsername->id != $id)
				return $this->failResourceExists(lang('Errors.users.already_exists_username', ['username' => $data['username']]));
		}

		if(isset($data['email_address'])) {
			$duplicateEmail = $this->users->getUserByEmailAddress($data['email_address']);
			if($duplicateEmail && $duplicateEmail->id != $id)
				return $this->failResourceExists(lang('Errors.users.already_exists_email_address', ['email_address' => $data['email_address']]));
		}

		// If included, hash password
		if(isset($data['password'])) {
			$data['password'] = password_hash(
				$data['password'],
				PASSWORD_BCRYPT,
				['cost' => 10]
			);
		}

		$this->users->update($id, $data);

		return $this->respondUpdated($this->users->getUser($id));
	}

	/**
	 * To delete a user
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function delete($id) {
		if($this->settings->getSetting('is_demo') == 1)
			return $this->fail(lang('Errors.demo.cannot_delete_users'));

		if(!$this->users->find($id))
			return $this->failNotFound(lang('Errors.users.not_found', ['id' => $id]));

		// Make sure user isn't trying to delete his own account
		if($this->logged_user->id == $id)
			return $this->fail(lang('Errors.users.own_account'));

		$this->users->delete($id);

		return $this->respondDeleted([
			'id' => $id
		]);
	}

	/**
	 * To add a warehouse to user
	 * 
	 * Method			PUT
	 * Filter			auth:admin
	 */
	public function add_warehouse($userId, $warehouseId) {
		$user = $this->users->find($userId);

		if(!$user)
			return $this->failNotFound(lang('Errors.users.not_found', ['id' => $userId]));
		
		if($user->role != 'supervisor' && $user->role != 'worker')
			return $this->fail(lang('Errors.users.not_supervisor_worker'));

		if(!$this->warehouses->find($warehouseId))
			return $this->failNotFound(lang('Errors.users.not_found_warehouse', ['id' => $warehouseId]));

		if($this->warehouse_relations->findRelation($userId, $warehouseId))
			return $this->fail(lang('Errors.users.already_exists_warehouse_relation'));

		$data = [
			'user_id' => $userId,
			'warehouse_id' => $warehouseId
		];

		$warehouse_relation_id = $this->warehouse_relations->insert($data);
		$warehouse_relation = $this->warehouse_relations->find($warehouse_relation_id);

		return $this->respondUpdated($warehouse_relation);
	}

	/**
	 * To remove a warehouse from user
	 * 
	 * Method			DELETE
	 * Filter			auth:admin
	 */
	public function remove_warehouse($userId, $warehouseId) {
		$user = $this->users->find($userId);

		if(!$user)
			return $this->failNotFound(lang('Errors.users.not_found', ['id' => $userId]));
		
		if($user->role != 'supervisor' && $user->role != 'worker')
			return $this->fail(lang('Errors.users.not_supervisor_worker'));

		if(!$this->warehouses->find($warehouseId))
			return $this->failNotFound(lang('Errors.users.not_found_warehouse', ['id' => $warehouseId]));

		if(!$this->warehouse_relations->findRelation($userId, $warehouseId))
			return $this->fail(lang('Errors.users.not_found_warehouse_relation'));

		$this->warehouse_relations->deleteRelation($userId, $warehouseId);
		
		return $this->respondDeleted([
			'userId' => $userId,
			'warehouseId' => $warehouseId
		]);
	}

	/**
	 * To get a list of warehouses that this user doesn't have access to
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 */
	public function pending_warehouses_list($userId) {
		// Make sure user exists, and it's a worker/supervisor
		$user = $this->users->find($userId);

		if(!$user)
			return $this->failNotFound(lang('Errors.users.not_found', ['id' => $userId]));

		if($user->role != 'supervisor' && $user->role != 'worker')
			return $this->fail(lang('Errors.users.not_supervisor_worker'));

		return $this->respond($this->warehouses->getWarehousesUserIsNotResponsible($userId));
	}

	//$routes->get(			'(:num)/pending-warehouses',			'Users::pending_warehouses_list',	['filter' => 'backend_auth:admin']);

	/**
	 * To export a CSV file with all users (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of users, with as much information as we can get
		$users = $this->users->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "users__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($users, $filename));
	}
}