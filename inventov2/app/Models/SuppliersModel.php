<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class SuppliersModel extends Model {
	protected $table = 'inventov2_suppliers';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
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

	// To get all suppliers -- Adapted to DataTables
	public function dtGetAllSuppliers() {
		$recordsTotal = $this
			->select('inventov2_suppliers.*')
			->countAllResults();

		$suppliers = $this
			->select('id AS DT_RowId,
								name,
								internal_name,
								company_name,
								email_address,
								phone_number,
								vat')
			->groupStart()
			->orLike('id', $this->dtSearch)
			->orLike('name', $this->dtSearch)
			->orLike('internal_name', $this->dtSearch)
			->orLike('company_name', $this->dtSearch)
			->orLike('email_address', $this->dtSearch)
			->orLike('phone_number', $this->dtSearch)
			->orLike('vat', $this->dtSearch)
			->groupEnd()
			->orderBy($this->dtOrderBy, $this->dtOrderDir)
			->limit($this->dtLength, $this->dtStart)
			->groupBy('id');

		$recordsFiltered = $suppliers->countAllResults(false);
		$data = $suppliers->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get 5 most recent suppliers -- Without DataTables features
	// Results will vary depending on the user that is requesting
	// them (if $limitByWarehouses is true, we'll limit results to the
	// warehouse IDs provided in $warehouseIds)
	public function dtGetLatest(bool $limitByWarehouses = false, array $warehouseIds = []) {
		$data = $this
			->select('inventov2_suppliers.id AS DT_RowId,
								inventov2_suppliers.created_at,
								inventov2_suppliers.name,
								inventov2_suppliers.internal_name,
								inventov2_suppliers.company_name,
								inventov2_suppliers.email_address')
			->orderBy('inventov2_suppliers.created_at', 'DESC')
			->limit(5);

		// Should we limit by warehouse?
		if($limitByWarehouses)
			$data = $this->restrictQueryByIds($data, 'inventov2_suppliers.warehouse_id', $warehouseIds);

		$recordsFiltered = $data->countAllResults(false);
		$recordsTotal = $recordsFiltered;
		$data = $data->find();

		return [
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data
		];
	}

	// To get a detailed list of all suppliers
	public function getDetailedList() {
		$suppliers = $this
			->select('inventov2_suppliers.id,
								inventov2_suppliers.name,
								inventov2_suppliers.internal_name,
								inventov2_suppliers.company_name,
								inventov2_suppliers.vat,
								inventov2_suppliers.email_address,
								inventov2_suppliers.phone_number,
								inventov2_suppliers.address,
								inventov2_suppliers.city,
								inventov2_suppliers.country,
								inventov2_suppliers.state,
								inventov2_suppliers.zip_code,
								inventov2_suppliers.custom_field1,
								inventov2_suppliers.custom_field2,
								inventov2_suppliers.custom_field3,
								inventov2_suppliers.notes,
								inventov2_suppliers.created_by,
								_user.username AS created_by_username,
								_user.name AS created_by_name,
								inventov2_suppliers.created_at,
								inventov2_suppliers.updated_at')
			->join('inventov2_users AS _user', '_user.id = inventov2_suppliers.created_by', 'left')
			->orderBy('inventov2_suppliers.id', 'ASC')
			->find();

		if(!$suppliers)
			return [];
		
		return $suppliers;
	}

	// To get a single supplier by ID
	public function getSupplier($id) {
		$supplier = $this
			->select('inventov2_suppliers.id,
								inventov2_suppliers.name,
								inventov2_suppliers.internal_name,
								inventov2_suppliers.company_name,
								inventov2_suppliers.vat,
								inventov2_suppliers.email_address,
								inventov2_suppliers.phone_number,
								inventov2_suppliers.address,
								inventov2_suppliers.city,
								inventov2_suppliers.country,
								inventov2_suppliers.state,
								inventov2_suppliers.zip_code,
								inventov2_suppliers.custom_field1,
								inventov2_suppliers.custom_field2,
								inventov2_suppliers.custom_field3,
								inventov2_suppliers.notes,
								inventov2_suppliers.created_at,
								inventov2_suppliers.updated_at,
								inventov2_suppliers.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_suppliers.created_by', 'left')
			->where('inventov2_suppliers.id', $id)
			->first();

		if(!$supplier)
			return false;

		$grouper = new JsonGrouper('created_by', $supplier);

		return $grouper->group();
	}

	// To get a single supplier by name
	public function getSupplierByName($name) {
		$supplier = $this
			->select('inventov2_suppliers.id,
								inventov2_suppliers.name,
								inventov2_suppliers.internal_name,
								inventov2_suppliers.company_name,
								inventov2_suppliers.vat,
								inventov2_suppliers.email_address,
								inventov2_suppliers.phone_number,
								inventov2_suppliers.address,
								inventov2_suppliers.city,
								inventov2_suppliers.country,
								inventov2_suppliers.state,
								inventov2_suppliers.zip_code,
								inventov2_suppliers.custom_field1,
								inventov2_suppliers.custom_field2,
								inventov2_suppliers.custom_field3,
								inventov2_suppliers.notes,
								inventov2_suppliers.created_at,
								inventov2_suppliers.updated_at,
								inventov2_suppliers.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_suppliers.created_by', 'left')
			->where('inventov2_suppliers.name', $name)
			->first();

		if(!$supplier)
			return false;

		$grouper = new JsonGrouper('created_by', $supplier);

		return $grouper->group();
	}

	// To get a single supplier by internal name
	public function getSupplierByInternalName($internalName) {
		$supplier = $this
			->select('inventov2_suppliers.id,
								inventov2_suppliers.name,
								inventov2_suppliers.internal_name,
								inventov2_suppliers.company_name,
								inventov2_suppliers.vat,
								inventov2_suppliers.email_address,
								inventov2_suppliers.phone_number,
								inventov2_suppliers.address,
								inventov2_suppliers.city,
								inventov2_suppliers.country,
								inventov2_suppliers.state,
								inventov2_suppliers.zip_code,
								inventov2_suppliers.custom_field1,
								inventov2_suppliers.custom_field2,
								inventov2_suppliers.custom_field3,
								inventov2_suppliers.notes,
								inventov2_suppliers.created_at,
								inventov2_suppliers.updated_at,
								inventov2_suppliers.created_by AS created_by_id,
								_user.name AS created_by_name')
			->join('inventov2_users AS _user', '_user.id = inventov2_suppliers.created_by', 'left')
			->where('inventov2_suppliers.internal_name', $internalName)
			->first();

		if(!$supplier)
			return false;

		$grouper = new JsonGrouper('created_by', $supplier);

		return $grouper->group();
	}

	// To get a list of suppliers (id and name), primarily to be displayed in a select
	public function getSuppliersList() {
		$suppliers = $this->select('id, name')->find();

		if(!$suppliers)
			return [];

		return $suppliers;
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