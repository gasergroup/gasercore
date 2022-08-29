<?php namespace App\Controllers\Backend;

class Locales extends BaseController {
	public function set_locale() {
		$locale = $this->request->getVar('locale');

		// Get available locales, and make sure new one exists
		$locales = getLocalesAvailable();

		if(!in_array($locale, $locales)) {
			// Doesn't exist, set default
			$locale = $this->settings->getSetting('default_locale');
			$this->session->set(['inventov2_locale' => $locale]);
		}else{
			// Exists, set new one
			$this->session->set(['inventov2_locale' => $locale]);
		}

		return $this->respond([
			'locale' => $locale
		]);
	}
}