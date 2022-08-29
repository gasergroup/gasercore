<?php namespace App\Models;

use CodeIgniter\Model;

class WarehouseRelationsModel extends Model {
	protected $table = 'inventov2_warehouse_relations';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'user_id',
		'warehouse_id'
	];

	protected $useTimestamps = false;
	protected $useSoftDeletes = false;

	// To get a list of warehouses $userId is responsible of
	public function getWarehousesByUser($userId) {
		$warehouses = $this
			->select('_warehouse.id AS id,
								_warehouse.name AS name')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_warehouse_relations.warehouse_id', 'inner')
			->join('inventov2_users AS _user', '_user.id = inventov2_warehouse_relations.user_id', 'inner')
			->where('user_id', $userId)
			->find();

		if(!$warehouses)
			return false;

		return $warehouses;
	}

	// To get a list of warehouse IDs $userId is responsible of
	public function getWarehouseIdsByUser($userId) {
		$warehouseIds = $this->where('user_id', $userId)->findColumn('warehouse_id');

		if(!$warehouseIds)
			return [];

		return $warehouseIds;
	}

	// To get a relation between userId and warehouseId
	public function findRelation($userId, $warehouseId) {
		return $this->where('user_id', $userId)->where('warehouse_id', $warehouseId)->find();
	}

	// To delete a relation between userId and warehouseId
	public function deleteRelation($userId, $warehouseId) {
		return $this->where('user_id', $userId)->where('warehouse_id', $warehouseId)->delete();
	}

	// To delete relation between a warehouse and all users
	public function deleteWarehouseRelations($warehouseId) {
		return $this->where('warehouse_id', $warehouseId)->delete();
	}

	// To get list of workers responsible of a given warehouse ID
	public function getWorkersResponsibleOfWarehouse($warehouseId) {
		$workers = $this
			->select('_user.id AS id,
								_user.name AS name,
								_user.username AS username')
			->join('inventov2_users AS _user', "_user.id = inventov2_warehouse_relations.user_id", 'left')
			->where('inventov2_warehouse_relations.warehouse_id', $warehouseId)
			->where('_user.role', 'worker')
			->groupBy('_user.id')
			->find();

		if(!$workers)
			return [];

		return $workers;
	}

	// To get list of supervisors responsible of a given warehouse ID
	public function getSupervisorsResponsibleOfWarehouse($warehouseId) {
		$supervisors = $this
			->select('_user.id AS id,
								_user.name AS name,
								_user.username AS username')
			->join('inventov2_users AS _user', "_user.id = inventov2_warehouse_relations.user_id", 'left')
			->where('inventov2_warehouse_relations.warehouse_id', $warehouseId)
			->where('_user.role', 'supervisor')
			->groupBy('_user.id')
			->find();

		if(!$supervisors)
			return [];

		return $supervisors;
	}
}