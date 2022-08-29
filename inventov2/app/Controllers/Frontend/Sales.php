<?php namespace App\Controllers\Frontend;

class Sales extends BaseController {
	
	public function index($saleId = false) {
		$this->data['route'] = 'sales';
		$this->data['saleId'] = $saleId;

		return view('sales/sales', $this->data);
	}

	public function new() {
		$this->data['route'] = 'sales';

		return view('sales/new_sale', $this->data);
	}

	public function returns($returnId = false) {
		$this->data['route'] = 'sales-returns';
		$this->data['returnId'] = $returnId;

		return view('sales/sales_returns', $this->data);
	}

	public function new_return($saleId = false) {
		$this->data['route'] = 'sales-returns';
		$this->data['saleId'] = $saleId;

		return view('sales/new_sale_return', $this->data);
	}
}