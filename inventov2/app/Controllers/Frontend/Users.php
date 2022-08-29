<?php namespace App\Controllers\Frontend;

class Users extends BaseController {

	public function index($userId = false) {
		$this->data['route'] = 'users';
		$this->data['userId'] = $userId;

		return view('users/users', $this->data);
	}

	public function new() {
		$this->data['route'] = 'users';

		return view('users/new_user', $this->data);
	}
}