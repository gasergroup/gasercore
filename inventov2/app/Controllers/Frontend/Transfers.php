<?php namespace App\Controllers\Frontend;

class Transfers extends BaseController {
	
	public function index($transferId = false) {
		$this->data['route'] = 'transfers';
		$this->data['transferId'] = $transferId;

		return view('transfers/transfers', $this->data);
	}

	public function new() {
		$this->data['route'] = 'transfers';

		return view('transfers/new_transfer', $this->data);
	}
}