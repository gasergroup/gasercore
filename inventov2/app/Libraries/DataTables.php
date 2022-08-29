<?php namespace App\Libraries;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Custom library to interact with DataTables
 * 
 * Here we'll receive data from DataTables, and validate
 * everything according to what the controller needs. We'll
 * provide with easy-to-use methods, so that the controller
 * can ask for any information it needs
 * 
 * @author Ricardo Yubal <support@sglancer.com>
 */
class DataTables {
	private $orderCols = [];
	private $isRequestValid = false;

	/**
	 * Constructor - Gets crucial information (request and allowed order by columns)
	 * 
	 * @param IncomingRequest $request - The incoming request
	 * @param array $orderCols - Allowed order by columns
	 */
	public function __construct(IncomingRequest $request, array $orderCols) {
		// Save request
		$this->request = $request;

		// Retrieve information DataTables should've sent us... We'll validate it
		// afterwards
		$this->dt_draw = $this->request->getVar('draw') ?? false;
		$this->dt_start = $this->request->getVar('start') ?? false;
		$this->dt_length = $this->request->getVar('length') ?? false;
		$this->dt_search = $this->request->getVar('search') ?? false;
		$this->dt_order = $this->request->getVar('order') ?? false;
		$this->dt_columns = $this->request->getVar('columns') ?? false;

		// Save allowed order columns
		$this->orderCols = $orderCols;
	}

	/**
	 * Makes sure all required parameters are present in the request
	 * 
	 * @return bool isRequestValid
	 */
	public function isRequestValid() : bool {
		if($this->dt_draw === false
			|| $this->dt_start == false
			|| $this->dt_length === false
			|| $this->dt_search === false
			|| $this->dt_order === false
			|| $this->dt_columns === false)
			$this->isRequestValid = false;
		$this->isRequestValid = true;

		return $this->isRequestValid;
	}

	/**
	 * Returns DataTables "draw" parameter
	 * 
	 * @return bool|int False if the request is invalid, int (draw) otherwise
	 */
	public function getDraw() {
		if(!$this->isRequestValid)
			return false;

		return $this->dt_draw;
	}

	/**
	 * Returns DataTables search string
	 * 
	 * @return string Empty if the request is invalid, search string otherwise
	 */
	public function getSearchStr() {
		if(!$this->isRequestValid)
			return '';
		
		return $this->dt_search['value'] ?? '';
	}

	/**
	 * Returns DataTables order by column
	 * 
	 * @return bool|string False if the request or column is invalid, column name otherwise
	 */
	public function getOrderBy() {
		if(!$this->isRequestValid)
			return false;

		$orderCol = $this->dt_order[0]['column'] ?? false;

		if($orderCol === false)
			return false;

		if($orderCol >= count($this->orderCols))
			return false;
		
		return $this->orderCols[$orderCol];
	}

	/**
	 * Returns DataTables order direction
	 * 
	 * @return bool|string False if the request or direction is invalid, direction otherwise
	 */
	public function getOrderDir() {
		if(!$this->isRequestValid)
			return false;
		
		$orderDir = $this->dt_order[0]['dir'] ?? false;

		if($orderDir === false)
			return false;

		if($orderDir != 'desc' && $orderDir != 'asc')
			return false;
		
		return $orderDir;
	}

	/**
	 * Returns DataTables length parameter - How many records to return per page
	 * 
	 * @return int Length
	 */
	public function getLength() {
		if(!$this->isRequestValid)
			return 0;

		return $this->dt_length;
	}

	/**
	 * Returns DataTables start parameter - From which index to start fetching results
	 * 
	 * @return int Start
	 */
	public function getStart() {
		if(!$this->isRequestValid)
			return 0;

		return $this->dt_start;
	}
}