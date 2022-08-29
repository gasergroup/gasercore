<?php namespace App\Controllers\Backend;

class Settings extends BaseController {
	
	private $rules = [
		'references_style' => [
			'rules' => 'in_list[increasing,random]',
			'errors' => [
				'in_list' => 'Validation.settings.references_style_in_list'
			]
		],
		'references_increasing_length' => [
			'rules' => 'numeric|greater_than_equal_to[0]',
			'errors' => [
				'numeric' => 'Validation.settings.references_increasing_length_numeric',
				'greater_than_equal_to' => 'Validation.settings.references_increasing_length_greater_than_equal_to'
			]
		],
		'references_random_chars' => [
			'rules' => 'min_length[1]',
			'errors' => [
				'min_length' => 'Validation.settings.references_random_chars_min_length'
			]
		],
		'references_random_chars_length' => [
			'rules' => 'numeric|greater_than_equal_to[4]',
			'errors' => [
				'numeric' => 'Validation.settings.references_random_chars_length_numeric',
				'greater_than_equal_to' => 'Validation.settings.references_random_chars_length_greater_than_equal_to'
			]
		],
		'references_sale_append' => [
			'rules' => 'permit_empty'
		],
		'references_sale_prepend' => [
			'rules' => 'permit_empty'
		],
		'references_purchase_append' => [
			'rules' => 'permit_empty'
		],
		'references_purchase_prepend' => [
			'rules' => 'permit_empty'
		],
		'references_purchase_return_append' => [
			'rules' => 'permit_empty'
		],
		'references_purchase_return_prepend' => [
			'rules' => 'permit_empty'
		],
		'references_sale_return_append' => [
			'rules' => 'permit_empty'
		],
		'references_sale_return_prepend' => [
			'rules' => 'permit_empty'
		],
		'jwt_secret_key' => [
			'rules' => 'min_length[20]',
			'errors' => [
				'min_length' => 'Validation.settings.jwt_secret_key_min_length'
			]
		],
		'jwt_exp' => [
			'rules' => 'numeric',
			'errors' => [
				'numeric' => 'Validation.settings.jwt_exp_numeric'
			]
		],
		'site_title' => [
			'rules' => 'min_length[1]',
			'errors' => [
				'min_length' => 'Validation.settings.site_title_min_length'
			]
		],
		'default_locale' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.settings.default_locale_required'
			]
		],
		'currency_name' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.settings.currency_name_required'
			]
		],
		'currency_symbol' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.settings.currency_symbol_required'
			]
		]
	];

	/**
	 * To get all currencies
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function currencies() {
		return $this->respond([
			'currencies' => $this->currencies->findAll()
		]);
	}

	/**
	 * To update settings
	 * 
	 * Method			PUT
	 * Filter			auth:admin
	 */
	public function update() {
		if($this->settings->getSetting('is_demo') == 1)
			return $this->fail(lang('Errors.demo.cannot_update_settings'));

		helper('locales');

		if(!$this->validateRequestWithRules($this->rules))
			return $this->failWithValidationErrors();

		$updateFields = [
			'references_style',
			'references_increasing_length',
			'references_random_chars',
			'references_random_chars_length',
			'references_sale_append',
			'references_sale_prepend',
			'references_purchase_prepend',
			'references_purchase_prepend',
			'references_purchase_return_append',
			'references_purchase_return_prepend',
			'references_sale_return_append',
			'references_sale_return_prepend',
			'jwt_secret_key',
			'jwt_exp',
			'site_title',
			'default_locale',
			'currency_name',
			'currency_symbol'
		];

		$data = $this->buildUpdateArray($updateFields, true);

		// Make sure default_locale is valid
		$locales = getLocalesAvailable();
		if(!in_array($data['default_locale'], $locales))
			return $this->failValidationErrors(lang('Validation.settings.default_locale_not_found', ['default_locale' => $data['default_locale']]));
		
		// For append/prepends of returns, one of them must be present
		if($data['references_purchase_return_append'] == '' && $data['references_purchase_return_prepend'] == '')
			return $this->failValidationErrors(lang('Validation.settings.references_purchase_return_missing_append_prepend'));

		if($data['references_sale_return_append'] == '' && $data['references_sale_return_prepend'] == '')
			return $this->failValidationErrors(lang('Validation.settings.references_sale_return_missing_append_prepend'));

		foreach($data as $name => $val)
			$this->settings->set('val', $val)->where('name', $name)->update();

		return $this->respond([
			'status' => 'ok'
		]);
	}

	/**
	 * To generate a random JWT
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 */
	public function generate_random_jwt() {
		$allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$jwt = '';
		for($i = 0; $i < 24; $i++)
			$jwt .= $allowedChars[rand(0, strlen($allowedChars)-1)];
		
		return $this->respond([
			'jwt' => $jwt
		]);
	}

	/**
	 * To upload and set a new site logo
	 * 
	 * Method			PUT
	 * Filter			auth:admin
	 */
	public function upload_logo() {
		if($this->settings->getSetting('is_demo') == 1)
			return $this->fail(lang('Errors.demo.cannot_upload_logo'));

		$logo = $this->request->getFile('logo');

		// Error while trying to upload?
		if(!$logo->isValid()) {
			return $this->fail([
				"error_string" => $logo->getErrorString(),
				"error" => $logo->getError()
			]);
		}

		$tmpLogo = $logo->getTempName();
		$mimeType = $logo->getMimeType();

		// Check MIME type
		if($mimeType != 'image/png')
			return $this->fail(lang('Errors.settings.logo_invalid_mime'));

		// Get image dimensions
		list($width, $height, $type, $attr) = getimagesize($tmpLogo);
		if($width != 510 || $height != 135)
			return $this->fail(lang('Errors.settings.logo_invalid_dims'));

		// Upload!
		$newPath = FCPATH . "assets\images\logo";
		$logo->move($newPath, 'logo.png', true);

		return $this->respond([
			'status' => 'ok'
		]);
	}
}