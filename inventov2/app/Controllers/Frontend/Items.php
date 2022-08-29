<?php namespace App\Controllers\Frontend;

class Items extends BaseController {

	public function index($itemId = false) {
		$this->data['route'] = 'items';

		$this->data['categories'] = $this->categories->getCategoriesList();
		$this->data['brands'] = $this->brands->getBrandsList();
		$this->data['suppliers'] = $this->suppliers->getSuppliersList();
		$this->data['itemId'] = $itemId;

		return view('items/items', $this->data);
	}

	public function new() {
		$this->data['route'] = 'items';

		$this->data['categories'] = $this->categories->getCategoriesList();
		$this->data['brands'] = $this->brands->getBrandsList();

		return view('items/new_item', $this->data);
	}
}