<?php namespace App\Libraries;

/**
 * Custom library to generate unique references (purchases, sales, returns)
 * 
 * @author Ricardo Yubal <support@sglancer.com>
 */
class ReferenceGenerator {
	// Style when generating references -- increasing or random
	public $ref_style = 'increasing';
	
	// If references are increasing, this will set a minimum length
	public $ref_increasing_length = 5;

	// Characters to be used when generating a random reference
	public $ref_random_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

	// If references are random, this will set a fixed length
	public $ref_random_chars_length = 15;

	// Prepend string for sale references
	public $ref_sale_prepend = 'REF-';

	// Append string for sale references
	public $ref_sale_append = '';

	// Prepend string for purchase references
	public $ref_purchase_prepend = 'REF-';

	// Append string for purchase references
	public $ref_purchase_append = '';

	// If we'll be generating increasing references, we'll keep track of the latest
	// reference generated here
	public $ref_current_number = 0;

	// Prepend for purchase return references
	public $ref_purchase_return_prepend = '';

	// Append for purchase return references
	public $ref_purchase_return_append = '-P-RET';

	// Prepend for sale return references
	public $ref_sale_return_prepend = '';

	// Append for sale return references
	public $ref_sale_return_append = '-S-RET';

	// When creating new instance, load all reference settings
	public function __construct() {
		$this->settings = new \App\Models\SettingsModel();
		$this->sales = new \App\Models\SalesModel();
		$this->purchases = new \App\Models\PurchasesModel();
		$this->sales_returns = new \App\Models\SalesReturnsModel();
		$this->purchases_returns = new \App\Models\PurchasesReturnsModel();

		$settings = $this->settings->getReferencesSettings();

		$this->ref_style = $settings->references_style;
		$this->ref_increasing_length = $settings->references_increasing_length;
		$this->ref_random_chars = $settings->references_random_chars;
		$this->ref_random_chars_length = $settings->references_random_chars_length;
		$this->ref_sale_prepend = $settings->references_sale_prepend;
		$this->ref_sale_append = $settings->references_sale_append;
		$this->ref_purchase_prepend = $settings->references_purchase_prepend;
		$this->ref_purchase_append = $settings->references_purchase_append;
		$this->ref_current_number = $settings->references_current_number;
		$this->ref_purchase_return_prepend = $settings->references_purchase_return_prepend;
		$this->ref_purchase_return_append = $settings->references_purchase_return_append;
		$this->ref_sale_return_prepend = $settings->references_sale_return_prepend;
		$this->ref_sale_return_append = $settings->references_sale_return_append;
	}

	/**
	 * To generate a unique reference
	 * 
	 * @param string $type - Reference type (sale, purchase, sale_return, purchase_return)
	 * @return string Reference
	 */
	public function generate($type) {
		if($this->ref_style == 'increasing') {
			// Get the biggest existing reference. If it doesn't exist already,
			// this is the one we'll use
			$latest_ref = $this->settings->getSetting('references_current_number');

			// If reference is shorter than minimum length, fill voids with 0
			if(strlen($latest_ref) < $this->ref_increasing_length)
				$new_ref = str_pad($latest_ref, $this->ref_increasing_length, "0", STR_PAD_LEFT);
			else
				$new_ref = $latest_ref;
			$new_ref_str = $this->appendPrependToReference($new_ref, $type);

			// Check if sale/purchase/sale return/purchase return with this
			// reference exists.. If it doesn't, this will be our reference
			if(!$this->doesReferenceExist($new_ref_str)) {
				// It doesn't.. This will be the reference, we're done
				return $new_ref_str;
			}

			// It exists.. We need to generate a new ref with +1
			$latest_ref += 1;
			if(strlen($latest_ref) < $this->ref_increasing_length)
				$new_ref = str_pad($latest_ref, $this->ref_increasing_length, "0", STR_PAD_LEFT);
			else
				$new_ref = $latest_ref;
			$new_ref_str = $this->appendPrependToReference($new_ref, $type);

			// If at this point it still exists, save new reference and re-run function
			if($this->doesReferenceExist($new_ref_str)) {
				$this->settings->setSetting('references_current_number', $latest_ref);
				return $this->generate($type);
			}
			
			return $new_ref_str;
		}

		if($this->ref_style == 'random') {
			// Generate random reference
			$ref = '';
			for($i = 0; $i < $this->ref_random_chars_length; $i++)
				$ref .= $this->ref_random_chars[rand(0, strlen($this->ref_random_chars)-1)];

			// If it exists, re-run function
			if($this->doesReferenceExist($ref))
				return $this->generate($type);
			
			return $this->appendPrependToReference($ref, $type);
		}
	}

	// To check if reference exists
	private function doesReferenceExist($ref) {
		if($this->sales->getSaleByReference($ref)
				|| $this->purchases->getPurchaseByReference($ref)
				|| $this->sales_returns->getReturnByReference($ref)
				|| $this->purchases_returns->getReturnByReference($ref))
			return true;
		return false;
	}

	// To prepend and append corresponding strings to reference
	private function appendPrependToReference($reference, $type) {
		if($type == 'sale')
			return "{$this->ref_sale_prepend}{$reference}{$this->ref_sale_append}";
		else if($type == 'purchase')
			return "{$this->ref_purchase_prepend}{$reference}{$this->ref_purchase_append}";
		else if($type == 'sale_return')
			return "{$this->ref_sale_return_prepend}{$reference}{$this->ref_sale_return_append}";
		else if($type == 'purchase_return')
			return "{$this->ref_purchase_return_prepend}{$reference}{$this->ref_purchase_return_append}";
		return "";
	}
}