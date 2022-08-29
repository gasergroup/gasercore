<?php namespace App\Controllers\Frontend;

class Alerts extends BaseController {

	public function index() {
		$this->data['route'] = 'alerts';

		return view('alerts/alerts', $this->data);
	}
}