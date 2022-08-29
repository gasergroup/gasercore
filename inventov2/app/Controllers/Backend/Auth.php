<?php namespace App\Controllers\Backend;

use Firebase\JWT\JWT;

/**
 * Auth-related requests: login, etc
 */
class Auth extends BaseController {
	
	/**
	 * To log into the system
	 * 
	 * Method			POST
	 * Filter			None
	 * 
	 */
	public function login() {
		$username = $this->request->getVar('username');
		$password = $this->request->getVar('password');
		$type = $this->request->getVar('type');

		if($type != 'session' && $type != 'jwt')
			return $this->failValidationErrors(lang('Errors.auth.wrong_type'));

		$user = $this->users
			->where('username', $username)
			->first();
		
		if(!$user)
			return $this->failUnauthorized(lang('Errors.auth.wrong_credentials'));

		if(!password_verify($password, $user->password))
			return $this->failUnauthorized(lang('Errors.auth.wrong_credentials'));

		$jwt_secret_key = $this->settings->getSetting('jwt_secret_key');
		$jwt_exp = $this->settings->getSetting('jwt_exp');
		$time = time();

		$payload = [
			'iat' => $time,
			'exp' => $time + $jwt_exp,
			'claims' => [
				'id' => $user->id,
				'name' => $user->name,
				'username' => $user->username,
				'email_address' => $user->email_address,
				'role' => $user->role
			]
		];

		$jwt = JWT::encode($payload, $jwt_secret_key, 'HS256');

		// If we're trying to login with session, let's store the JWT
		if($type == 'session')
			$this->session->set(['inventov2_jwt' => $jwt]);

		return $this->respond([
			'jwt' => $jwt
		]);
	}
}