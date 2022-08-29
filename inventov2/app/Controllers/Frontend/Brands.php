<?php namespace App\Controllers\Frontend;

class Brands extends BaseController {

	public function index($brandId = false) {
		$this->data['route'] = 'brands';
		$this->data['brandId'] = $brandId;

		return view('brands/brands', $this->data);
	}

	public function new() {
		$this->data['route'] = 'brands';

		return view('brands/new_brand', $this->data);
	}
}