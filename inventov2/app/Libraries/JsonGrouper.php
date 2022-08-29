<?php namespace App\Libraries;

/**
 * There are times when a MySQL result is generated with values that have something
 * in common and can be grouped together when converting them to JSON. This will
 * take care of that.
 * 
 * For example, we generated an array of objects like this:
 * {
 * 		id: 1,
 * 		created_by_id: 1,
 * 		created_by_name: name
 * }
 * 
 * This class will help convert those objects to:
 * {
 * 		id: 1,
 * 		created_by: {
 * 			id: 1,
 * 			name: name
 * 		}
 * }
 * 
 * It can work with both arrays (multiple results) and objects (single result)
 * 
 * @author Ricardo Yubal <support@sglancer.com>
 */
class JsonGrouper {
	// Array with the columns that we will group
	private $columns;

	// Array with the data
	private $data;
	
	/**
	 * Constructor - We'll get columns and data
	 * 
	 * @param string|array $columns - The columns
	 * @param array|object $data - The data
	 */
	public function __construct($columns, $data) {
		if(!is_array($columns))
			$this->columns = [$columns];
		else
			$this->columns = $columns;

		$this->data = $data;
	}

	/**
	 * This will actually group the data and return it ready
	 * 
	 * @return array|object Grouped result
	 */
	public function group() {
		$result = [];

		if(is_array($this->data))
			return $this->groupArray();
		
		if(is_object($this->data))
			return $this->groupObject();

		return $result;
	}

	/**
	 * Group the data from an array
	 * 
	 * @return array Grouped result
	 */
	public function groupArray() {
		$result = [];

		foreach($this->data as $row) {
			$reshaped = [];

			foreach($row as $rowName => $rowValue) {
				// Does this rowName have a prefix of the included in columns?
				$rowPrefix = $this->getColumnPrefix($rowName);

				if($rowPrefix) {
					// Found it. Let's remove the prefix from rowName
					$rowNameWithoutPrefix = substr($rowName, strlen("{$rowPrefix}_"));

					$reshaped[$rowPrefix][$rowNameWithoutPrefix] = $rowValue;
				}else{
					// Didn't find anything in common.. Let's just pass the data as is
					$reshaped[$rowName] = $rowValue;
				}
			}

			$result[] = $reshaped;
		}

		return $result;
	}

	/**
	 * Group the data from an object
	 * 
	 * @return object Grouped result
	 */
	public function groupObject() {
		$reshaped = [];

		// Cast object as array
		$dataArr = get_object_vars($this->data);

		foreach($dataArr as $rowName => $rowValue) {
			// Does this rowName have a prefix of the included in columns?
			$rowPrefix = $this->getColumnPrefix($rowName);

			if($rowPrefix) {
				// Found it. Let's remove the prefix from rowName
				$rowNameWithoutPrefix = substr($rowName, strlen("{$rowPrefix}_"));

				$reshaped[$rowPrefix][$rowNameWithoutPrefix] = $rowValue;
			}else{
				// Didn't find anything in common.. Let's just pass the data as is
				$reshaped[$rowName] = $rowValue;
			}
		}

		// Cast inner arrays as objects
		foreach($reshaped as $rowName => &$rowValue) {
			if(is_array($rowValue))
				$rowValue = (object) $rowValue;
		}

		return (object) $reshaped;
	}

	/**
	 * This function will use the internal $columns array, and will take
	 * a $theColumn to search.
	 * $columns example: ['one', 'two', 'three']
	 * 
	 * We'll loop through $columns in this manner, checking if a substring
	 * of $column matches one of them:
	 * ['one_', 'two_', 'three_']
	 * 
	 * So, if in this example $column was 'two_field_name', this will return
	 * 'two'. Otherwise false.
	 */
	private function getColumnPrefix($theColumn) {
		foreach($this->columns as $column) {
			$newStr = "{$column}_";
			if(substr($theColumn, 0, strlen($newStr)) == $newStr)
				return $column;
		}
		return false;
	}
}