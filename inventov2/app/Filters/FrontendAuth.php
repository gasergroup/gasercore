<?php namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Config\Services;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

/**
 * This class will take care of filtering requests that require auth,
 * before they even get to the controller.
 * It will get the JWT from the session, parse it, and compare its
 * information against the DB. If any of the details is incorrect,
 * the request will end here.
 * What we do from here, after a failed validation, will depend
 * on the kind of filtering:
 * 
 * auth - Redirect to the login page
 * supervisor/admin - Show 404 or error page
 */
class FrontendAuth implements FilterInterface {
	use ResponseTrait;

	public function __construct() {
		// Add response service, so ResponseTrait can use it
		$this->response = service('response');

		$this->settings = new \App\Models\SettingsModel();
		$this->users = new \App\Models\UsersModel();
		$this->session = \Config\Services::session();
	}

	// Authentication filter
	// If $arguments is null, we'll only check that the JWT is valid (and that the account exists)
	// If $arguments has admin, we'll check the user is an admin
	// If $arguments has worker, we'll check the user is a worker
	public function before(RequestInterface $request, $arguments = null) {

		$jwt_secret_key = $this->settings->getSetting('jwt_secret_key');
		
		$token = $this->session->get('inventov2_jwt');

		// Make sure we've got a JWT
		if(!$token)
			return redirect()->to('/login');

		// Make sure it can be decoded
		try {
			$decoded = JWT::decode($token, $jwt_secret_key, ['HS256']);
		}catch(ExpiredException $e) {
			$this->session->remove('inventov2_jwt');
			return redirect()->to('/login');
		}catch(SignatureInvalidException $e) {
			return redirect()->to('/logout');
		}

		// Make sure we've got the components we need
		$areComponentsSet = isset(
			$decoded->iat,
			$decoded->exp,
			$decoded->claims->id,
			$decoded->claims->name,
			$decoded->claims->username,
			$decoded->claims->email_address,
			$decoded->claims->role
		);

		if(!$areComponentsSet) {
			$this->session->remove('inventov2_jwt');
			return redirect()->to('/login');
		}

		// At this point, the JWT is valid. Compare against user role if needed
		if($arguments) {
			$role = $decoded->claims->role ?? false;

			$compAdmin = in_array('admin', $arguments) && $role == 'admin';
			$compSupervisor = in_array('supervisor', $arguments) && $role == 'supervisor';
			$compWorker = in_array('worker', $arguments) && $role == 'worker';

			// No role match? End request here
			if(!$compAdmin && !$compSupervisor && !$compWorker)
				return redirect()->to('/401');
		}

		// Finally, match the account against the DB.. Making sure it exists,
		// the role matches, and the user is not deleted
		$user_id = $decoded->claims->id;
		$user_role = $decoded->claims->role;
		$user = $this->users->where('role', $user_role)->find($user_id);

		if(!$user)
			return redirect()->to('/401');
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
		
	}
}