<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * All requests will be passed through this filter before they get
 * to the controller.
 * Here, we will return CORS headers. Then, if the request method was OPTIONS,
 * we'll end the request here, since we sent already everything we needed
 * to send (just headers)
 */
class CORS implements FilterInterface {
	public function before(RequestInterface $request, $arguments = null) {
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Headers, Access-Control-Request-Method, Authorization, Content-Length, Accept-Encoding, X-CSRF-Token');
		header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH');

		if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
			die();
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {

	}
}