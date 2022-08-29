<?php namespace App\Controllers\Frontend;

class Settings extends BaseController {

	public function index() {
		helper('locales');

		$this->data['route'] = 'settings';

		$this->data['locales'] = getLocalesAvailable();
		$this->data['currencies'] = $this->currencies->orderBy('name', 'asc')->findAll();

		/*
		$this->data['categories'] = $this->categories->getCategoriesList();
		$this->data['brands'] = $this->brands->getBrandsList();
		$this->data['suppliers'] = $this->suppliers->getSuppliersList();
		$this->data['itemId'] = $itemId;
		*/

		return view('settings/settings', $this->data);
	}
}