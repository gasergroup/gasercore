<?php namespace App\Controllers\Frontend;

class Warehouses extends BaseController {

	public function index($warehouseId = false) {
		$this->data['route'] = 'warehouses';
		$this->data['warehouseId'] = $warehouseId;

		return view('warehouses/warehouses', $this->data);
	}

	public function new() {
		$this->data['route'] = 'warehouses';

		return view('warehouses/new_warehouse', $this->data);
	}
}