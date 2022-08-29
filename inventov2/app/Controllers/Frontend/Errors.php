<?php namespace App\Controllers\Frontend;

class Errors extends BaseController {

	public function show_404() {
		//return view('brands/brands', $this->data);
		echo view('errors/error_404');
		//return view('errors/error_404', $this->data);
	}

	public function show_401() {
		echo view('errors/error_401');
	}
}