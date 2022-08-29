<?php namespace App\Controllers\Frontend;

class Adjustments extends BaseController {
	
	public function index($adjustmentId = false) {
		$this->data['route'] = 'adjustments';
		$this->data['adjustmentId'] = $adjustmentId;

		return view('adjustments/adjustments', $this->data);
	}

	public function new() {
		$this->data['route'] = 'adjustments';

		return view('adjustments/new_adjustment', $this->data);
	}
}