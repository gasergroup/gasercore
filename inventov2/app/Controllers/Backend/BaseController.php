<?php namespace App\Controllers\Backend;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;

class BaseController extends Controller {
	// To be able to send responses
	use ResponseTrait;

	// Whenever we receive a request, we'll load here information (if it
	// exists) about the user's session
	public $jwt = null;
	public $jwt_iat = null;
	public $jwt_exp = null;
	public $logged_user = null;

	// Constructor
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
		parent::initController($request, $response, $logger);

		// Preload all models, libraries and services we might need
		$this->adjustments = new \App\Models\AdjustmentsModel();
		$this->alerts = new \App\Models\AlertsModel();
		$this->brands = new \App\Models\BrandsModel();
		$this->categories = new \App\Models\CategoriesModel();
		$this->currencies = new \App\Models\CurrenciesModel();
		$this->customers = new \App\Models\CustomersModel();
		$this->items = new \App\Models\ItemsModel();
		$this->item_suppliers = new \App\Models\ItemSuppliersModel();
		$this->purchases = new \App\Models\PurchasesModel();
		$this->purchases_returns = new \App\Models\PurchasesReturnsModel();
		$this->quantities = new \App\Models\QuantitiesModel();
		$this->sales = new \App\Models\SalesModel();
		$this->sales_returns = new \App\Models\SalesReturnsModel();
		$this->settings = new \App\Models\SettingsModel();
		$this->suppliers = new \App\Models\SuppliersModel();
		$this->transfers = new \App\Models\TransfersModel();
		$this->users = new \App\Models\UsersModel();
		$this->warehouses = new \App\Models\WarehousesModel();
		$this->warehouse_relations = new \App\Models\WarehouseRelationsModel();

		$this->validation = \Config\Services::validation();
		$this->session = \Config\Services::session();

		// Get JWT information from the DB
		$jwt_secret_key = $this->settings->getSetting('jwt_secret_key');
		//$jwt_exp = $this->settings->getSetting('jwt_exp');

		// If we have an authorization header in the request OR as a session,
		// attempt to decode the JWT and store its components. A filter
		// already took care of it, so at this point the WJT is still valid
		$token = false;
		$authHeader = $request->getServer('HTTP_AUTHORIZATION') ?? false;
		if($authHeader) {
			$authHeaderArr = explode(' ', $authHeader);
			$token = $authHeaderArr[1];
		}else{
			$token = $this->session->get('inventov2_jwt');
		}

		if($token) {
			try {
				$decoded = JWT::decode($token, $jwt_secret_key, ['HS256']);

				$this->jwt = $token;
				$this->jwt_iat = $decoded->iat;
				$this->jwt_exp = $decoded->exp;
				$this->logged_user = $decoded->claims;
			}catch(\Exception $e) { }
		}
	}

	// To validate a request with rules
	public function validateRequestWithRules($rules) {
		$this->validation->setRules($rules);
		return $this->validation->withRequest($this->request)->run();
	}

	// To return a HTTP fail error with validation errors
	public function failWithValidationErrors() {
		return $this->failValidationErrors($this->validation->getErrors());
	}

	/**
	 * To create a $data array with the information that has to be
	 * inserted into the DB whenever we want to create a new entry.
	 * We'll get the info from the IncomingRequest
	 * 
	 * NOTE: Null values will not be added to the array, so that
	 * optional fields that are missing will be inserted with
	 * default value
	 * 
	 * NOTE 2: getVar() doesn't return NULL when the item doesn't
	 * exist (contrary to what the docs say). Also, getRawInput
	 * doesn't decode information correctly.
	 * Until either of those things gets fixed, we'll be using
	 * file_get_contents directly (considering request will be
	 * sent as HTTP POST, raw json)
	 * 
	 * If input HTML should be converted to entities, set
	 * $sanitizeHtml to true
	 */
	public function buildCreateArray($columns, $sanitizeHtml = false) {
		if(!is_array($columns))
			$columns = [$columns];

		$json = json_decode(file_get_contents('php://input'), true);

		$data = [];

		if($json == null)
			return $data;

		foreach($columns as $column) {
			$field = $json[$column] ?? null;
			if($field !== null)
				$data[$column] = $field;
		}

		if($sanitizeHtml)
			return $this->sanitizeDataHtml($data);

		return $data;
	}

	/**
	 * To create a $data array with the information that has to be
	 * modified whenever we want to update an entry. We'll get the
	 * info from the IncomingRequest
	 * 
	 * NOTE: Null values will not be added to the array, so that
	 * we only modify columns that we've received in the request
	 * 
	 * NOTE 2: getvar() doesn't return NULL when the item doesn't
	 * exist (contrary to what the docs say). Also, getRawInput
	 * doesn't decode information correctly.
	 * Until either of those two things gets fixed, we'll be
	 * using file_get_contents directly (considering request will
	 * be sent as HTTP PUT, raw json)
	 * 
	 * If input HTML should be converted to entities, set
	 * $sanitizeHtml to true
	 */
	public function buildUpdateArray($columns, $sanitizeHtml = false) {
		if(!is_array($columns))
			$columns = [$columns];

		$json = json_decode(file_get_contents('php://input'), true);
		
		$data = [];

		if($json === null)
			return $data;
		
		foreach($columns as $column) {
			if(isset($json[$column]))
				$data[$column] = $json[$column];
		}

		if($sanitizeHtml)
			return $this->sanitizeDataHtml($data);

		return $data;
	}

	// To convert HTML characters to entities (sanitize)
	// ENT_NOQUOTES - Keep single and double quotes
	// ENT_HTML5 - Treat as HTML5
	public function sanitizeHtml(string $str) : string {
		return htmlspecialchars($str, ENT_NOQUOTES | ENT_HTML5, 'utf-8');
	}

	// To convert HTML characters to entities (from array of data)
	// This will allow nested information, up to 3 levels
	public function sanitizeDataHtml(array $data) : array {
		foreach($data as $name => &$val) {
			if(!is_array($val))
				$val = $this->sanitizeHtml($val);
			else {
				foreach($val as $name2 => &$val2) {
					if(!is_array($val2))
						$val2 = $this->sanitizeHtml($val2);
					else {
						foreach($val2 as $name3 => &$val3)
							$val3 = $this->sanitizeHtml($val3);
					}
				}
			}
		}
		
		return $data;
	}
}