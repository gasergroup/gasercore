<?php namespace App\Models;

use App\Libraries\JsonGrouper;
use CodeIgniter\Model;

class QuantitiesModel extends Model {
	protected $table = 'inventov2_quantities';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'item_id',
		'warehouse_id',
		'quantity'
	];

	protected $useTimestamps = false;
	protected $createdField = '';
	protected $updatedField = '';

	// Get quantities for an item, per each warehouse
	public function getItemQuantities($itemId) {
		$quantities = $this
			->select('inventov2_quantities.quantity,
								_warehouse.id AS warehouse_id,
								_warehouse.name AS warehouse_name')
			->join('inventov2_warehouses AS _warehouse', '_warehouse.id = inventov2_quantities.warehouse_id', 'left')
			->where('inventov2_quantities.item_id', $itemId)
			->groupBy('inventov2_quantities.warehouse_id')
			->find();

		if(!$quantities)
			return [];

		$grouper = new JsonGrouper(['warehouse'], $quantities);

		return $grouper->group();
	}

	// Get total amount of pieces (quantity) of an item, for all warehouses
	public function getItemTotalQuantities($itemId) {
		return $this
			->selectSum('quantity')
			->where('item_id', $itemId)
			->first()
			->quantity;
	}

	// Get quantities for an item, for a specific warehouse
	public function getItemQuantity($itemId, $warehouseId) {
		return $this
			->select('quantity')
			->where('item_id', $itemId)
			->where('warehouse_id', $warehouseId)
			->first()
			->quantity;
	}
	
	// Get total amount of items (quantity) in a warehouse
	public function getWarehouseTotalQty($warehouseId) {
		return $this
			->selectSum('quantity')
			->where('warehouse_id', $warehouseId)
			->first()
			->quantity;
	}

	// Delete all quantity records of a warehouse. Records have to be 0 qty
	public function deleteWarehouseQuantities($warehouseId) {
		return $this->where('warehouse_id', $warehouseId)->where('quantity = 0')->delete();
	}

	// Delete all quantity records of an item, for all warehouses
	// Records have to be 0 qty
	public function deleteItemQuantities($itemId) {
		return $this->where('item_id', $itemId)->where('quantity = 0')->delete();
	}

	// Add items to stock
	public function addStock($qtyToAdd, $itemId, $warehouseId) {
		return $this
			->set('quantity', "quantity + ${qtyToAdd}", false)
			->where('item_id', $itemId)
			->where('warehouse_id', $warehouseId)
			->update();
	}

	// Remove items from stock
	public function removeStock($qtyToRemove, $itemId, $warehouseId) {
		return $this
			->set('quantity', "quantity - ${qtyToRemove}", false)
			->where('item_id', $itemId)
			->where('warehouse_id', $warehouseId)
			->update();
	}
}