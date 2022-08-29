<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class CustomersModel extends Model {
	protected $table = 'inventov2_customers';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'name',
		'internal_name',
		'company_name',
		'tax_number',
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
		'notes',
		'created_by',
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

	// To get all customers -- Adapted to DataTables
	public function dtGetAllCustomers() {
		$recordsTotal = $this
			->select('inventov2_customers.*')
			->countAllResults();

		$customers = $this
			->select('inventov2_customers.id AS DT_RowId,
								inventov2_customers.name,
								inventov2_customers.internal_name,
								inventov2_customers.email_address,
								inventov2_customers.phone_number,
								inventov2_customers.tax_number')
			->groupStart()
			->orLike('inventov2_customers.name', $this->dtSearch)
			->orLike('inventov2_customers.internal_name', $this->dtSearch)
			->orLike('inventov2_customers.company_name', $this->dtSearch)
			->orLike('inventov2_customers.email_address', $this->dtSearch)
			->orLike('inventov2_customers.phone_number', $this->dtSearch)
			->orLike('inventov2_customers.tax_number', $this->dtSearch)
			->groupEnd()
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('inventov2_customers.id');

		$recordsFiltered = $customers->countAllResults(false);
		$data = $customers->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get 5 most recent customers -- Without DataTables features
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetLatest(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$data = $this
			->select('inventov2_customers.id AS DT_RowId,
								inventov2_customers.created_at,
								inventov2_customers.name,
								inventov2_customers.internal_name,
								inventov2_customers.company_name,
								inventov2_customers.email_address')
			->orderBy('inventov2_customers.created_at', 'DESC')
			->limit(5);

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$data = $this->restrictQueryByIds($data, 'inventov2_customers.warehouse_id', $warehouseIds);

		$recordsFiltered = $data->countAllResults(false);
		$recordsTotal = $recordsFiltered;
		$data = $data->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all customers
	public function getDetailedList() {
		$customers = $this
			->select('inventov2_customers.id,
								inventov2_customers.name,
								inventov2_customers.internal_name,
								inventov2_customers.company_name,
								inventov2_customers.tax_number,
								inventov2_customers.email_address,
								inventov2_customers.phone_number,
								inventov2_customers.address,
								inventov2_customers.city,
								inventov2_customers.country,
								inventov2_customers.state,
								inventov2_customers.zip_code,
								inventov2_customers.custom_field1,
								inventov2_customers.custom_field2,
								inventov2_customers.custom_field3,
								inventov2_customers.notes,
								inventov2_customers.created_by,
								_user.username AS created_by_username,
								_user.name AS created_by_name,
								inventov2_customers.created_at,
								inventov2_customers.updated_at')
			->join('inventov2_users AS _user', '_user.id = inventov2_customers.created_by', 'left')
			->orderBy('inventov2_customers.id', 'ASC')
			->find();

		if(!$customers)
			return [];
		
		return $customers;
	}

	// To get a single customer by ID
	public function getCustomer($id) {
		$customer = $this
			->select('inventov2_customers.id,
								inventov2_customers.name,
								inventov2_customers.internal_name,
								inventov2_customers.company_name,
								inventov2_customers.tax_number,
								inventov2_customers.email_address,
								inventov2_customers.phone_number,
								inventov2_customers.address,
								inventov2_customers.city,
								inventov2_customers.country,
								inventov2_customers.state,
								inventov2_customers.zip_code,
								inventov2_customers.custom_field1,
								inventov2_customers.custom_field2,
								inventov2_customers.custom_field3,
								inventov2_customers.notes,
								inventov2_customers.created_at,
								inventov2_customers.updated_at,
								inventov2_customers.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_customers.created_by', 'left')
			->where('inventov2_customers.id', $id)
			->first();

		if(!$customer)
			return false;

		$grouper = new JsonGrouper('created_by', $customer);

		return $grouper->group();
	}

	// To get a single customer by name
	public function getCustomerByName($name) {
		$customer = $this
			->select('inventov2_customers.id,
								inventov2_customers.name,
								inventov2_customers.internal_name,
								inventov2_customers.company_name,
								inventov2_customers.tax_number,
								inventov2_customers.email_address,
								inventov2_customers.phone_number,
								inventov2_customers.address,
								inventov2_customers.city,
								inventov2_customers.country,
								inventov2_customers.state,
								inventov2_customers.zip_code,
								inventov2_customers.custom_field1,
								inventov2_customers.custom_field2,
								inventov2_customers.custom_field3,
								inventov2_customers.notes,
								inventov2_customers.created_at,
								inventov2_customers.updated_at,
								inventov2_customers.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_customers.created_by', 'left')
			->where('inventov2_customers.name', $name)
			->first();

		if(!$customer)
			return false;

		$grouper = new JsonGrouper('created_by', $customer);

		return $grouper->group();
	}

	// To get a single customer by internal name
	public function getCustomerByInternalName($internalName) {
		$customer = $this
			->select('inventov2_customers.id,
								inventov2_customers.name,
								inventov2_customers.internal_name,
								inventov2_customers.company_name,
								inventov2_customers.tax_number,
								inventov2_customers.email_address,
								inventov2_customers.phone_number,
								inventov2_customers.address,
								inventov2_customers.city,
								inventov2_customers.country,
								inventov2_customers.state,
								inventov2_customers.zip_code,
								inventov2_customers.custom_field1,
								inventov2_customers.custom_field2,
								inventov2_customers.custom_field3,
								inventov2_customers.notes,
								inventov2_customers.created_at,
								inventov2_customers.updated_at,
								inventov2_customers.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_customers.created_by', 'left')
			->where('inventov2_customers.internal_name', $internalName)
			->first();

		if(!$customer)
			return false;

		$grouper = new JsonGrouper('created_by', $customer);

		return $grouper->group();
	}

	// To get a list of customers (id and name), primarily to be displayed in a select
	public function getCustomersList() {
		$customers = $this->select('id, name')->find();

		if(!$customers)
			return [];

		return $customers;
	}

	/**
	 * This function will restrict a query, so that $column only has
	 * the values provided in the $ids array
	 */
	private function restrictQueryByIds($query, string $column, array $ids) {
		if(count($ids) == 0)
			$query->where('1=0', null, false);
		else{
			$query->groupStart();
			foreach($ids as $id)
				$query->orWhere($column, $id);
			$query->groupEnd();
		}

		return $query;
	}
}