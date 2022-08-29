<?php namespace App\Models;

use CodeIgniter\Model;
use stdClass;

class SettingsModel extends Model {
	protected $table = 'inventov2_settings';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'name',
		'val'
	];

	protected $useTimestamps = false;

	public function getSetting($name) {
		return $this->where('name', $name)->first()->val;
	}

	public function setSetting($name, $val) {
		return $this->where('name', $name)->set('val', $val)->update();
	}

	// To get all settings
	public function getSettings() {
		$_settings = $this->find();

		$settings= [];
		foreach($_settings as $setting)
			$settings[$setting->name] = $setting->val;

		return (object)$settings;
	}

	public function getReferencesSettings() {
		$_settings = $this
			->orWhere('name', 'references_style')
			->orWhere('name', 'references_increasing_length')
			->orWhere('name', 'references_random_chars')
			->orWhere('name', 'references_random_chars_length')
			->orWhere('name', 'references_sale_prepend')
			->orWhere('name', 'references_sale_append')
			->orWhere('name', 'references_purchase_prepend')
			->orWhere('name', 'references_purchase_append')
			->orWhere('name', 'references_current_number')
			->orWhere('name', 'references_purchase_return_prepend')
			->orWhere('name', 'references_purchase_return_append')
			->orWhere('name', 'references_sale_return_prepend')
			->orWhere('name', 'references_sale_return_append')
			->find();

		if(!$_settings)
			return new stdClass;

		$settings = [];
		foreach($_settings as $setting)
			$settings[$setting->name] = $setting->val;

		return (object)$settings;
	}

	// To add 1 to the latest reference generated
	public function addOneTwoLatestReference() {
		return $this->where('name', 'references_current_number')->set('val', 'val+1', false)->update();
	}
}