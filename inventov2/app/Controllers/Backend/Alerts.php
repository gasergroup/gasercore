<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;

class Alerts extends BaseController {

	/**
	 * To get all alerts
	 * 
	 * Method			GET
	 * Filter			auth:admin
	 * 
	 */
	public function index() {
		$columns = ['item_name', 'warehouse_name', 'type', 'alert_qty', 'current_qty', 'created_at'];
		$datatables = new DataTables($this->request, $columns);

		if($datatables->isRequestValid() === false)
			return $this->failUnauthorized(lang('Errors.unauthorized'));
		
		$draw = $datatables->getDraw();
		$length = $datatables->getLength();
		$start = $datatables->getStart();
		$search = $datatables->getSearchStr();
		$orderBy = $datatables->getOrderBy();
		$orderDir = $datatables->getOrderDir();

		if($orderBy === false || $orderDir === false)
			return $this->fail(lang('Errors.invalid_order'));

		$this->alerts->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		return $this->respond(array_merge(
			['draw' => $draw],
			$this->alerts->dtGetAllAlerts()
		));
	}
}