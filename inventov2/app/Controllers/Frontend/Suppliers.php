<?php namespace App\Controllers\Frontend;

class Suppliers extends BaseController {

	public function index($supplierId = false) {
		$this->data['route'] = 'suppliers';

		/*
		$this->data['categories'] = $this->categories->getCategoriesList();
		$this->data['brands'] = $this->brands->getBrandsList();
		$this->data['suppliers'] = $this->suppliers->getSuppliersList();
		$this->data['itemId'] = $itemId;
		*/
		$this->data['supplierId'] = $supplierId;

		return view('suppliers/suppliers', $this->data);
	}

	public function new() {
		$this->data['route'] = 'suppliers';

		/*
		$this->data['categories'] = $this->categories->getCategoriesList();
		$this->data['brands'] = $this->brands->getBrandsList();
		*/

		return view('suppliers/new_supplier', $this->data);
	}
}