<?php namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Config\Services;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

/**
 * This class will analyze if we have a "locale" element saved in the session.
 * If we don't, or if we do but it's not valid, we'll set it with the
 * default locale from our settings table.
 * 
 * Then, we'll see if we have an Accept-Language header, and we'll attempt to use
 * its value instead (if it's valid). If it's not valid, we'll use what we
 * set previously in the session.
 */
class Locale implements FilterInterface {
	use ResponseTrait;

	public function __construct() {
		// Add response service, so ResponseTrait can use it
		$this->response = service('response');

		$this->settings = new \App\Models\SettingsModel();
		$this->session = \Config\Services::session();
		$this->language = \Config\Services::language();
	}

	public function before(RequestInterface $request, $arguments = null) {
		// Get list of locales available
		helper('locales');
		$langs = getLocalesAvailable();
		
		// Do we have a locale in the session?
		$locale = $this->session->get('inventov2_locale');
		if($locale) {
			// We do.. If it's not valid, set the default from the settings table
			if(!in_array($locale, $langs)) {
				$locale = $this->settings->getSetting('default_locale');
				$this->session->set(['inventov2_locale' => $locale]);
			} 
		}else{
			// We don't, set the default from the settings table
			$locale = $this->settings->getSetting('default_locale');
			$this->session->set(['inventov2_locale' => $locale]);
		}
		
		// Do we have an Accept-Language header?
		$acceptLanguage = $request->getServer('HTTP_ACCEPT_LANGUAGE');
		if($acceptLanguage) {
			// We do.. Use it only if it's valid
			if(in_array($acceptLanguage, $langs))
				$locale = $acceptLanguage;
		}

		// Use the selected locale
		$this->language->setLocale($locale);
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
		
	}
}