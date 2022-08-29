<?php namespace App\Controllers\Frontend;

class Customers extends BaseController {
	
	public function index($customerId = false) {
		$this->data['route'] = 'customers';

		$this->data['customerId'] = $customerId;

		return view('customers/customers', $this->data);
	}

	public function new() {
		$this->data['route'] = 'customers';

		return view('customers/new_customer', $this->data);
	}
}