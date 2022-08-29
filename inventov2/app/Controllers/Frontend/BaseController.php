<?php

namespace App\Controllers\Frontend;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Psr\Log\LoggerInterface;

class BaseController extends Controller {

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
		$this->users = new \App\Models\UsersModel();
		$this->warehouses = new \App\Models\WarehousesModel();
		$this->warehouse_relations = new \App\Models\WarehouseRelationsModel();

		$this->validation = \Config\Services::validation();
		$this->session = \Config\Services::session();

		helper('locales');
		helper('lang');

		// Get JWT information from the DB
		$jwt_secret_key = $this->settings->getSetting('jwt_secret_key');

		// If we have JWT in the session, attempt to decode it and store its
		// components. A filter already took care of it, so at this point
		// the JWT is still valid
		$jwt = $this->session->get('inventov2_jwt') ?? false;
		if($jwt) {
			try {
				$decoded = JWT::decode($jwt, $jwt_secret_key, ['HS256']);

				$this->jwt = $jwt;
				$this->jwt_iat = $decoded->iat;
				$this->jwt_exp = $decoded->exp;
				$this->logged_user = $decoded->claims;
			}catch(\Exception $e) { }
		}

		$alerts = [];
		$alerts_count = 0;
		if($this->logged_user && $this->logged_user->role == 'admin') {
			$alerts = $this->alerts->getLatestAlertsForHeader();
			$alerts_count = $this->alerts->countAll();
		}

		// Load information our frontend will need -- Create our data array
		$this->data = [
			'logged_user' => $this->logged_user,
			'settings' => $this->settings->getSettings(),
			'current_locale' => $this->session->get('inventov2_locale'),
			'locales' => getLocalesAvailable(),
			'alerts' => $alerts,
			'alerts_count' => $alerts_count
		];
	}
}
