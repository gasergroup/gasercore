<?php namespace App\Models;

use CodeIgniter\Model;

class ItemSuppliersModel extends Model {
	protected $table = 'inventov2_item_suppliers';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'item_id',
		'supplier_id',
		'part_number',
		'price',
		'tax'
	];

	protected $useTimestams = false;
	protected $createField = '';
	protected $updateField = '';

	// Get suppliers for an item
	public function getItemSuppliers($itemId) {
		$suppliers = $this
			->select('inventov2_item_suppliers.supplier_id AS id,
								inventov2_item_suppliers.part_number,
								inventov2_item_suppliers.price,
								inventov2_item_suppliers.tax,
								_supplier.name AS name')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_item_suppliers.supplier_id', 'left')
			->where('inventov2_item_suppliers.item_id', $itemId)
			->groupBy('inventov2_item_suppliers.supplier_id')
			->find();

		if(!$suppliers)
			return [];

		return $suppliers;
	}

	// Get supplier for a particular item
	public function getItemSupplier($itemId, $supplierId) {
		$supplier = $this
			->select('inventov2_item_suppliers.supplier_id AS id,
								inventov2_item_suppliers.part_number,
								inventov2_item_suppliers.price,
								inventov2_item_suppliers.tax,
								_supplier.name AS name')
			->join('inventov2_suppliers AS _supplier', '_supplier.id = inventov2_item_suppliers.supplier_id', 'left')
			->where('inventov2_item_suppliers.item_id', $itemId)
			->where('inventov2_item_suppliers.supplier_id', $supplierId)
			->groupBy('inventov2_item_suppliers.supplier_id')
			->first();

		if(!$supplier)
			return false;

		return $supplier;
	}

	// To update a item-supplier relation
	public function updateItemSupplier($itemId, $supplierId, $data) {
		return $this
			->where('item_id', $itemId)
			->where('supplier_id', $supplierId)
			->set($data)
			->update();
	}

	// To remove a item-supplier relation
	public function removeItemSupplier($itemId, $supplierId) {
		return $this->where('item_id', $itemId)->where('supplier_id', $supplierId)->delete();
	}

	// To delete all suplier relations of an item
	public function deleteItemSuppliers($itemId) {
		$this->where('item_id', $itemId)->delete();
	}

	// To delete all supplier relations for a supplier
	public function deleteSupplierRelations($supplierId) {
		$this->where('supplier_id', $supplierId)->delete();
	}
}