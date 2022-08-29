<?php namespace App\Controllers\Frontend;

class Categories extends BaseController {

	public function index($categoryId = false) {
		$this->data['route'] = 'categories';
		$this->data['categoryId'] = $categoryId;

		return view('categories/categories', $this->data);
	}

	public function new() {
		$this->data['route'] = 'categories';

		return view('categories/new_category', $this->data);
	}
}