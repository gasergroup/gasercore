<?php namespace App\Controllers\Backend;

use CodeIgniter\API\ResponseTrait;

class Info extends BaseController {
	public function index() {
		return $this->respond(['data' => 'It works!']);
	}
}