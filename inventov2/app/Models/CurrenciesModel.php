<?php namespace App\Models;

use CodeIgniter\Model;

class CurrenciesModel extends Model {
	protected $table = 'inventov2_currencies';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $allowedFields = [
		'id',
		'full_name',
		'name',
		'symbol'
	];

	protected $useTimestamps = false;
	protected $useSoftDeletes = false;
}