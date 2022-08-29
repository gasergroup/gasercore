<?php namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model {
	protected $table = 'inventov2_users';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'name',
		'username',
		'password',
		'email_address',
		'phone_number',
		'role',
		'created_at',
		'updated_at',
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

	// To get all users -- Adapted to DataTables
	public function dtGetAllUsers() {
		$recordsTotal = $this
			->select('inventov2_users.*')
			->countAllResults();

		$users = $this
			->select('inventov2_users.id AS DT_RowId,
								name,
								username,
								email_address,
								phone_number,
								role,
								COUNT(_warehouse_relations.id) AS warehouses')
			->groupStart()
			->orLike('name', $this->dtSearch)
			->orLike('username', $this->dtSearch)
			->orLike('email_address', $this->dtSearch)
			->orLike('phone_number', $this->dtSearch)
			->orLike('role', $this->dtSearch)
			->groupEnd()
			->join('inventov2_warehouse_relations AS _warehouse_relations', '_warehouse_relations.user_id = inventov2_users.id', 'left')
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_users.id');

		$recordsFiltered = $users->countAllResults(false);
		$data = $users->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all users
	public function getDetailedList() {
		$users = $this
			->select('inventov2_users.id,
								inventov2_users.name,
								inventov2_users.username,
								inventov2_users.email_address,
								inventov2_users.phone_number,
								inventov2_users.role,
								inventov2_users.created_at,
								inventov2_users.updated_at')
			->orderBy('inventov2_users.id', 'ASC')
			->find();

		if(!$users)
			return [];
		
		return $users;
	}

	// To get a single user by ID
	public function getUser($id) {
		$user = $this
			->select('id,
								name,
								username,
								email_address,
								phone_number,
								role,
								created_at,
								updated_at')
			->where('inventov2_users.id', $id)
			->first();

		if(!$user)
			return false;

		return $user;
	}

	// To get a single user by username
	public function getUserByUsername($username) {
		$user = $this
			->select('id,
								name,
								username,
								email_address,
								phone_number,
								role,
								created_at,
								updated_at')
			->where('inventov2_users.username', $username)
			->first();

		if(!$user)
			return false;

		return $user;
	}

	// To get a single user by email address
	public function getUserByEmailAddress($email_address) {
		$user = $this
			->select('id,
								name,
								username,
								email_address,
								phone_number,
								role,
								created_at,
								updated_at')
			->where('inventov2_users.email_address', $email_address)
			->first();

		if(!$user)
			return false;

		return $user;
	}

	// To get a list of workers that are NOT responsible of a given warehouse ID
	public function getWorkersNotResponsibleOfWarehouse($warehouseId) {
		$workers = $this
			->select('inventov2_users.id AS id,
								inventov2_users.name AS name,
								inventov2_users.username AS username')
			->join('inventov2_warehouse_relations AS _relation', "_relation.user_id = inventov2_users.id AND _relation.warehouse_id = $warehouseId", 'left')
			->where('inventov2_users.role', 'worker')
			->where('inventov2_users.deleted_at is null')
			->where('_relation.user_id is null')
			->groupBy('inventov2_users.id')
			->find();

		if(!$workers)
			return [];
		
		return $workers;
	}

	// To get a list of supervisors that are NOT responsible of a given warehouse ID
	public function getSupervisorsNotResponsibleOfWarehouse($warehouseId) {
		$supervisors = $this
			->select('inventov2_users.id AS id,
								inventov2_users.name AS name,
								inventov2_users.username AS username')
			->join('inventov2_warehouse_relations AS _relation', "_relation.user_id = inventov2_users.id AND _relation.warehouse_id = $warehouseId", 'left')
			->where('inventov2_users.role', 'supervisor')
			->where('inventov2_users.deleted_at is null')
			->where('_relation.user_id is null')
			->groupBy('inventov2_users.id')
			->find();

	if(!$supervisors)
		return [];
	
	return $supervisors;
	}
}