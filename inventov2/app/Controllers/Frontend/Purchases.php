<?php namespace App\Controllers\Frontend;

class Purchases extends BaseController {

	public function index($purchaseId = false) {
		$this->data['route'] = 'purchases';
		$this->data['purchaseId'] = $purchaseId;

		return view('purchases/purchases', $this->data);
	}

	public function new() {
		$this->data['route'] = 'purchases';

		return view('purchases/new_purchase', $this->data);
	}

	public function returns($returnId = false) {
		$this->data['route'] = 'purchases-returns';
		$this->data['returnId'] = $returnId;

		return view('purchases/purchases_returns', $this->data);
	}

	public function new_return($purchaseId = false) {
		$this->data['route'] = 'purchases-returns';
		$this->data['purchaseId'] = $purchaseId;

		return view('purchases/new_purchase_return', $this->data);
	}
}