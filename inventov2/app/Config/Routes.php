<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
//$routes->setDefaultController('Frontend\Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');

/**
 * NOTE: CodeIgniter shared filters with the same URI, even if they are
 * defined separately with different HTTP verbs and filters. Because
 * of this, we will have to create verbose endpoints.
 * For example: /users/create instead of just a POST endpoint to /users/
 */

//$routes->get('api/info', 'Api\Info::index');

// All API requests will be routed through here
$routes->group('api', ['namespace' => 'App\Controllers\Backend'], function($routes) {
	// Info route - To make sure everything is working
	$routes->get('info', 'Info::index');

	// Login
	$routes->post('login', 'Auth::login');

	// To set new locale in session
	$routes->post('locale',			'Locales::set_locale');

	// To get alerts
	$routes->get('alerts',			'Alerts::index',			['filter' => 'backend_auth:admin']);

	// Quantity adjustments
	$routes->group('adjustments', function($routes) {
		$routes->post(	'',				'Adjustments::create',			['filter' => 'backend_auth:supervisor,admin']);
		$routes->get(		'(:num)',	'Adjustments::show/$1',			['filter' => 'backend_auth:supervisor,admin']);
		$routes->get(		'',				'Adjustments::index',				['filter' => 'backend_auth:supervisor,admin']);
	});

	// Transfers
	$routes->group('transfers', function($routes) {
		$routes->post(	'',					'Transfers::create',			['filter' => 'backend_auth:admin']);
		$routes->get(		'(:num)',		'Transfers::show/$1',			['filter' => 'backend_auth:supervisor,admin']);
		$routes->get(		'',					'Transfers::index',				['filter' => 'backend_auth:supervisor,admin']);
	});

	// Brands
	$routes->group('brands', function($routes) {
		$routes->get(		'export',		'Brands::export',			['filter' => 'backend_auth:admin']);
		$routes->put(		'(:num)',		'Brands::update/$1',	['filter' => 'backend_auth:supervisor,admin']);
		$routes->delete('(:num)',		'Brands::delete/$1',	['filter' => 'backend_auth:admin']);
		$routes->get(		'(:num)',		'Brands::show/$1',		['filter' => 'backend_auth']);
		$routes->get(		'',					'Brands::index',			['filter' => 'backend_auth']);
		$routes->post(	'',					'Brands::create',			['filter' => 'backend_auth']);
	});

	// Categories
	$routes->group('categories', function($routes) {
		$routes->get(		'export',		'Categories::export',			['filter' => 'backend_auth:admin']);
		$routes->put(		'(:num)',		'Categories::update/$1',	['filter' => 'backend_auth:supervisor,admin']);
		$routes->delete('(:num)',		'Categories::delete/$1',	['filter' => 'backend_auth:admin']);
		$routes->get(		'(:num)',		'Categories::show/$1',		['filter' => 'backend_auth']);
		$routes->get(		'',					'Categories::index',			['filter' => 'backend_auth']);
		$routes->post(	'',					'Categories::create',			['filter' => 'backend_auth']);
	});

	// Customers
	$routes->group('customers', function($routes) {
		$routes->get(		'export',					'Customers::export',							['filter' => 'backend_auth:admin']);
		$routes->get(		'list',						'Customers::list',								['filter' => 'backend_auth']);
		$routes->get(		'latest-table',		'Customers::show_latest_table',		['filter' => 'backend_auth:supervisor,admin']);
		$routes->put(		'(:num)',					'Customers::update/$1',						['filter' => 'backend_auth:supervisor,admin']);
		$routes->delete('(:num)',					'Customers::delete/$1',						['filter' => 'backend_auth:admin']);
		$routes->get(		'(:num)',					'Customers::show/$1',							['filter' => 'backend_auth']);
		$routes->get(		'',								'Customers::index',								['filter' => 'backend_auth']);
		$routes->post(	'',								'Customers::create',							['filter' => 'backend_auth']);
	});

	// Suppliers
	$routes->group('suppliers', function($routes) {
		$routes->get(		'export',					'Suppliers::export',							['filter' => 'backend_auth:admin']);
		$routes->get(		'list',						'Suppliers::list',								['filter' => 'backend_auth']);
		$routes->get(		'latest-table',		'Suppliers::show_latest_table',		['filter' => 'backend_auth:supervisor,admin']);
		$routes->put(		'(:num)',					'Suppliers::update/$1',						['filter' => 'backend_auth:supervisor,admin']);
		$routes->delete('(:num)',					'Suppliers::delete/$1',						['filter' => 'backend_auth:admin']);
		$routes->get(		'(:num)',					'Suppliers::show/$1',							['filter' => 'backend_auth']);
		$routes->get(		'',								'Suppliers::index',								['filter' => 'backend_auth']);
		$routes->post(	'',								'Suppliers::create',							['filter' => 'backend_auth']);
	});

	// Users
	$routes->group('users', function($routes) {
		$routes->get(			'export',													'Users::export',											['filter' => 'backend_auth:admin']);
		$routes->get(			'(:num)/pending-warehouses',			'Users::pending_warehouses_list/$1',	['filter' => 'backend_auth:admin']);
		$routes->put(			'(:num)/add-warehouse/(:num)',		'Users::add_warehouse/$1/$2',					['filter' => 'backend_auth:admin']);
		$routes->delete(	'(:num)/remove-warehouse/(:num)',	'Users::remove_warehouse/$1/$2',			['filter' => 'backend_auth:admin']);
		$routes->put(			'(:num)',													'Users::update/$1',										['filter' => 'backend_auth:admin']);
		$routes->delete(	'(:num)',													'Users::delete/$1',										['filter' => 'backend_auth:admin']);
		$routes->get(			'(:num)',													'Users::show/$1',											['filter' => 'backend_auth:admin']);
		$routes->get(			'',																'Users::index',												['filter' => 'backend_auth:admin']);
		$routes->post(		'',																'Users::create',											['filter' => 'backend_auth:admin']);
	});

	// Warehouses
	$routes->group('warehouses', function($routes) {
		$routes->get(			'export',												'Warehouses::export',													['filter' => 'backend_auth:admin']);
		$routes->get(			'list',													'Warehouses::list',														['filter' => 'backend_auth']);
		$routes->get(			'(:num)/pending-workers',				'Warehouses::pending_workers_list/$1',				['filter' => 'backend_auth:admin']);
		$routes->get(			'(:num)/pending-supervisors',		'Warehouses::pending_supervisors_list/$1',		['filter' => 'backend_auth:admin']);
		$routes->put(			'(:num)',												'Warehouses::update/$1',											['filter' => 'backend_auth:admin']);
		$routes->delete(	'(:num)',												'Warehouses::delete/$1',											['filter' => 'backend_auth:admin']);
		$routes->get(			'(:num)',												'Warehouses::show/$1',												['filter' => 'backend_auth:supervisor,admin']);
		$routes->get(			'',															'Warehouses::index/$1',												['filter' => 'backend_auth:supervisor,admin']);
		$routes->post(		'',															'Warehouses::create',													['filter' => 'backend_auth:admin']);
	});

	// Items
	$routes->group('items', function($routes) {
		$routes->get(			'export',											'Items::export',														['filter' => 'backend_auth:admin']);
		$routes->get(			'list',												'Items::list',															['filter' => 'backend_auth']);
		$routes->get(			'code/warehouse/(:num)',			'Items::show_by_code_with_warehouse/$1',		['filter' => 'backend_auth']);
		$routes->get(			'code',												'Items::show_by_code',											['filter' => 'backend_auth']);
		$routes->get(			'generate-code/(:segment)',		'Items::generate_code/$1',									['filter' => 'backend_auth:admin']);
		$routes->put(			'(:num)/supplier/(:num)',			'Items::update_supplier/$1/$2',							['filter' => 'backend_auth:admin']);
		$routes->delete(	'(:num)/supplier/(:num)',			'Items::remove_supplier/$1/$2',							['filter' => 'backend_auth:admin']);
		$routes->get(			'(:num)/supplier/(:num)',			'Items::show_supplier/$1/$2',								['filter' => 'backend_auth']);
		$routes->get(			'(:num)/warehouse/(:num)',		'Items::show_with_warehouse/$1/$2',					['filter' => 'backend_auth']);
		$routes->post(		'(:num)/add-supplier',				'Items::add_supplier/$1',										['filter' => 'backend_auth:supervisor,admin']);
		$routes->put(			'(:num)',											'Items::update/$1',													['filter' => 'backend_auth:admin']);
		$routes->delete(	'(:num)',											'Items::delete/$1',													['filter' => 'backend_auth:admin']);
		$routes->get(			'(:num)',											'Items::show/$1',														['filter' => 'backend_auth']);
		$routes->post(		'',														'Items::create',														['filter' => 'backend_auth:admin']);
		$routes->get(			'',														'Items::index',															['filter' => 'backend_auth']);
	});

	// Purchases
	$routes->group('purchases', function($routes) {
		$routes->get(			'export',							'Purchases::export',												['filter' => 'backend_auth:admin']);
		$routes->get(			'unique-reference',		'Purchases::generate_unique_reference',			['filter' => 'backend_auth']);
		$routes->get(			'reference',					'Purchases::show_by_reference',							['filter' => 'backend_auth']);
		$routes->get(			'returns/export',			'Purchases::export_returns',								['filter' => 'backend_auth:admin']);
		$routes->get(			'returns/(:num)',			'Purchases::show_return/$1',								['filter' => 'backend_auth']);
		$routes->get(			'returns',						'Purchases::show_returns',									['filter' => 'backend_auth']);
		$routes->get(			'latest-table',				'Purchases::show_latest_table',							['filter' => 'backend_auth:supervisor,admin']);
		$routes->post(		'(:num)/return',			'Purchases::return/$1',											['filter' => 'backend_auth']);
		$routes->get(			'(:num)/return',			'Purchases::show_return_by_purchase/$1',		['filter' => 'backend_auth']);
		$routes->get(			'(:num)',							'Purchases::show/$1',												['filter' => 'backend_auth']);
		$routes->post(		'',										'Purchases::create',												['filter' => 'backend_auth']);
		$routes->get(			'',										'Purchases::index',													['filter' => 'backend_auth']);
	});

	// Sales
	$routes->group('sales', function($routes) {
		$routes->get(			'export',							'Sales::export',											['filter' => 'backend_auth:admin']);
		$routes->get(			'unique-reference',		'Sales::generate_unique_reference',		['filter' => 'backend_auth']);
		$routes->get(			'reference',					'Sales::show_by_reference',						['filter' => 'backend_auth']);
		$routes->get(			'returns/export',			'Sales::export_returns',							['filter' => 'backend_auth:admin']);
		$routes->get(			'returns/(:num)',			'Sales::show_return/$1',							['filter' => 'backend_auth']);
		$routes->get(			'returns',						'Sales::show_returns',								['filter' => 'backend_auth']);
		$routes->get(			'latest-table',				'Sales::show_latest_table',						['filter' => 'backend_auth:supervisor,admin']);
		$routes->post(		'(:num)/return',			'Sales::return/$1',										['filter' => 'backend_auth']);
		$routes->get(			'(:num)/return',			'Sales::show_return_by_sale/$1',			['filter' => 'backend_auth']);
		$routes->get(			'(:num)',							'Sales::show/$1',											['filter' => 'backend_auth']);
		$routes->post(		'',										'Sales::create',											['filter' => 'backend_auth']);
		$routes->get(			'',										'Sales::index',												['filter' => 'backend_auth']);
	});

	// Stats
	$routes->group('stats', function($routes) {
		$routes->get('cash-flow/(:segment)',			'Stats::cash_flow/$1',				['filter' => 'backend_auth:supervisor,admin']);
		$routes->get('(:segment)', 								'Stats::index/$1',						['filter' => 'backend_auth:supervisor,admin']);
	});

	// Settings
	$routes->group('settings', function($routes) {
		$routes->post(	'upload-logo',			'Settings::upload_logo',						['filter' => 'backend_auth:admin']);
		$routes->get(		'random-jwt',				'Settings::generate_random_jwt',		['filter' => 'backend_auth:admin']);
		$routes->get(		'currencies',				'Settings::currencies',							['filter' => 'backend_auth:admin']);
		$routes->put(		'',									'Settings::update',									['filter' => 'backend_auth:admin']);
	});
});

// All frontend requests will be routed through here
$routes->group('', ['namespace' => 'App\Controllers\Frontend'], function($routes) {
	$routes->get('login',				'Login::index');
	$routes->get('logout',			'Login::logout');

	$routes->get('/',						'Dashboard::index',		['filter' => 'frontend_auth']);

	$routes->get('alerts',			'Alerts::index',			['filter' => 'frontend_auth:admin']);

	$routes->get('items',					'Items::index',				['filter' => 'frontend_auth']);
	$routes->get('items/new',			'Items::new',					['filter' => 'frontend_auth']);
	$routes->get('items/(:num)',	'Items::index/$1',		['filter' => 'frontend_auth']);

	$routes->get('suppliers',					'Suppliers::index',			['filter' => 'frontend_auth']);
	$routes->get('suppliers/new',			'Suppliers::new',				['filter' => 'frontend_auth']);
	$routes->get('suppliers/(:num)',	'Suppliers::index/$1',	['filter' => 'frontend_auth']);

	$routes->get('customers',					'Customers::index',			['filter' => 'frontend_auth']);
	$routes->get('customers/new',			'Customers::new',				['filter' => 'frontend_auth']);
	$routes->get('customers/(:num)',	'Customers::index/$1',	['filter' => 'frontend_auth']);

	$routes->get('categories',					'Categories::index',			['filter' => 'frontend_auth']);
	$routes->get('categories/new',			'Categories::new',				['filter' => 'frontend_auth']);
	$routes->get('categories/(:num)',		'Categories::index/$1',		['filter' => 'frontend_auth']);

	$routes->get('brands',					'Brands::index',			['filter' => 'frontend_auth']);
	$routes->get('brands/new',			'Brands::new',				['filter' => 'frontend_auth']);
	$routes->get('brands/(:num)',		'Brands::index/$1',		['filter' => 'frontend_auth']);

	$routes->get('warehouses',					'Warehouses::index',			['filter' => 'frontend_auth:supervisor,admin']);
	$routes->get('warehouses/new',			'Warehouses::new',				['filter' => 'frontend_auth:admin']);
	$routes->get('warehouses/(:num)',		'Warehouses::index/$1',		['filter' => 'frontend_auth:supervisor,admin']);

	$routes->get('users',					'Users::index',			['filter' => 'frontend_auth']);
	$routes->get('users/new',			'Users::new',				['filter' => 'frontend_auth']);
	$routes->get('users/(:num)',	'Users::index/$1',	['filter' => 'frontend_auth']);

	$routes->get('purchases',									'Purchases::index',						['filter' => 'frontend_auth']);
	$routes->get('purchases/new',							'Purchases::new',							['filter' => 'frontend_auth']);
	$routes->get('purchases/returns',					'Purchases::returns',					['filter' => 'frontend_auth']);
	$routes->get('purchases/returns/new',			'Purchases::new_return',			['filter' => 'frontend_auth']);
	$routes->get('purchases/returns/(:num)',	'Purchases::returns/$1',			['filter' => 'frontend_auth']);
	$routes->get('purchases/(:num)/return',		'Purchases::new_return/$1',		['filter' => 'frontend_auth']);
	$routes->get('purchases/(:num)',					'Purchases::index/$1',				['filter' => 'frontend_auth']);

	$routes->get('sales',									'Sales::index',						['filter' => 'frontend_auth']);
	$routes->get('sales/new',							'Sales::new',							['filter' => 'frontend_auth']);
	$routes->get('sales/returns',					'Sales::returns',					['filter' => 'frontend_auth']);
	$routes->get('sales/returns/new',			'Sales::new_return',			['filter' => 'frontend_auth']);
	$routes->get('sales/returns/(:num)',	'Sales::returns/$1',			['filter' => 'frontend_auth']);
	$routes->get('sales/(:num)/return',		'Sales::new_return/$1',		['filter' => 'frontend_auth']);
	$routes->get('sales/(:num)',					'Sales::index/$1',				['filter' => 'frontend_auth']);

	$routes->get('adjustments',					'Adjustments::index',				['filter' => 'frontend_auth:supervisor,admin']);
	$routes->get('adjustments/new',			'Adjustments::new',					['filter' => 'frontend_auth:supervisor,admin']);
	$routes->get('adjustments/(:num)',	'Adjustments::index/$1',		['filter' => 'frontend_auth:supervisor,admin']);

	$routes->get('transfers',						'Transfers::index',					['filter' => 'frontend_auth:supervisor,admin']);
	$routes->get('transfers/new',				'Transfers::new',						['filter' => 'frontend_auth:supervisor,admin']);
	$routes->get('transfers/(:num)',		'Transfers::index/$1',			['filter' => 'frontend_auth:supervisor,admin']);

	$routes->get('settings',							'Settings::index',				['filter' => 'frontend_auth:admin']);

	$routes->get('401',		'Errors::show_401');
});

$routes->set404Override('App\Controllers\Frontend\Errors::show_404');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
