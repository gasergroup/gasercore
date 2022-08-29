<?php namespace App\Controllers\Frontend;

class Login extends BaseController {

	public function index() {
		// Is user logged in? Redirect to main page
		if($this->logged_user)
			return redirect()->to('/dashboard');

		return view('login/login', $this->data);
	}

	public function logout() {
		// Unset session and redirect to the dashboard
		$this->session->remove('inventov2_jwt');

		return redirect()->to('/login');

		//$this->session->set(['inventov2_jwt' => $jwt]);
	}
}