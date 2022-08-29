<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;
use App\Libraries\ReferenceGenerator;

class Adjustments extends BaseController {

	// Define create rules
	private $rules = [
		'warehouse_id' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.adjustments.warehouse_id_required'
			]
		],
		'items' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.adjustments.items_required'
			]
		],
		'notes' => [
			'rules' => 'permit_empty'
		]
	];

	public function __construct() {
		
	}


	/**
	 * To get all quantity adjustments
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function index() {
		$columns = ['warehouse_name', 'created_by', 'created_at'];
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

		$this->adjustments->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->adjustments->dtGetAllAdjustments(true, $warehouseIds);
		}else{
			$result = $this->adjustments->dtGetAllAdjustments();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}


	/**
	 * To get a single quantity adjustment by ID
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show($id) {
		$adjustment = $this->adjustments->getAdjustment($id);

		if(!$adjustment)
			return $this->failNotFound(lang('Errors.adjustments.not_found', ['id' => $id]));

		// If user is supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $adjustment->warehouse->id))
				return $this->failUnauthorized(lang('Errors.adjustments.warehouse_unauthorized'));
		}

		return $this->respond($adjustment);
	}


	/**
	 * To create a new quantity adjustment
	 * 
	 * Method			POST
	 * Filter			auth:supervisor,admin
	*/
	public function create() {
		if(!$this->validateRequestWithRules($this->rules))
			return $this->failWithValidationErrors();

		$createFields = [
			'warehouse_id',
			'items',
			'notes'
		];

		$data = $this->buildCreateArray($createFields, true);

		// Make sure warehouse ID exists
		if(!$this->warehouses->getWarehouse($data['warehouse_id']))
			return $this->failNotFound(lang('Errors.adjustments.warehouse_not_found'));

		// If user is supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $data['warehouse_id']))
				return $this->failUnauthorized(lang('Errors.adjustments.warehouse_unauthorized'));
		}

		// Validate items
		if(!is_array($data['items']) || count($data['items']) == 0)
			return $this->failValidationErrors(lang('Errors.adjustments.items.malformed'));

		$itemsArr = $data['items'];

		/* Now we'll loop through the items, making sure it's well formed and:
			- All items exist
			- Info matches (id, name)
			- We have enough items in stock for subtractions
		*/
		for($i = 0; $i < count($data['items']); $i++) {
			// First, make sure all properties exist
			$itemProperties = array_keys($data['items'][$i]);
			$requiredProperties = ['id', 'name', 'code', 'quantity', 'adjustment_type', 'adjustment_quantity'];

			if(count(array_diff($itemProperties, $requiredProperties)) > 0)
				return $this->failValidationErrors(lang('Errors.adjustments.items.malformed'));

			// Convert to object to work better
			$itemObj = (object) $data['items'][$i];

			// Make sure item exists
			$item = $this->items->getItemWithWarehouse($itemObj->id, $data['warehouse_id']);
			if(!$item)
				return $this->failNotFound(lang('Errors.adjustments.items.not_found', ['id' => $itemObj->id]));

			// Make sure item info matches
			if($itemObj->name != $item->name
				|| $itemObj->code != $item->code
				|| $itemObj->quantity != $item->quantity)
				return $this->fail(lang('Errors.adjustments.items.inconsistent'));

			// Validate adjustment type
			if($itemObj->adjustment_type != 'subtract' && $itemObj->adjustment_type != 'add')
				return $this->fail(lang('Validation.adjustments.adjustment_type_invalid'));

			// Make sure adjustment quantity is numeric
			if(!$this->validation->check($itemObj->adjustment_quantity, 'numeric'))
				return $this->fail(lang('Validation.adjustments.adjustment_quantity_numeric'));

			// If this is a subtraction, make sure we've got enough items in stock
			if($itemObj->adjustment_type == 'subtract') {
				if($itemObj->adjustment_quantity > $item->quantity)
					return $this->fail(lang('Errors.adjustments.not_enough_qty'));
			}
		}

		// Make sure there are no duplicate items
		// For this, we'll extract the IDs, and then remove duplicates and compare number of
		// items
		$item_ids = [];
		foreach($data['items'] as $itemArr)
			$item_ids[] = $itemArr['id'];
		if(count(array_unique($item_ids)) < count($data['items']))
			return $this->fail(lang('Validation.adjustments.duplicate_items'));

		$data['created_by'] = $this->logged_user->id;
		$data['items'] = json_encode($data['items']);

		// Insert quantity adjustment
		$adjustment_id = $this->adjustments->insert($data);

		// Now let's update quantities in stock
		foreach($itemsArr as $itemArr) {
			if($itemArr['adjustment_type'] == 'subtract')
				$this->quantities->removeStock($itemArr['adjustment_quantity'], $itemArr['id'], $data['warehouse_id']);
			else
				$this->quantities->addStock($itemArr['adjustment_quantity'], $itemArr['id'], $data['warehouse_id']);
		}

		// Finally update quantity alerts
		$itemIds = [];
		foreach($itemsArr as $itemArr)
			$itemIds[] = $itemArr['id'];

		$this->updateAlerts($itemIds);

		// Done!
		$new_adjustment = $this->adjustments->getAdjustment($adjustment_id);

		// Done!
		return $this->respondCreated($new_adjustment);
	}


	/**
	 * To update quantity alerts for a given item
	 * 
	 * $itemIds can be a single ID, or an array of IDs
	 */
	private function updateAlerts($itemIds) {
		if(!is_array($itemIds))
			$itemIds = [$itemIds];

		foreach($itemIds as $itemId) {
			// Delete alerts (if they exist), because we'll create a new one
			// if we need to
			$this->alerts->deleteAlertsForItem($itemId);

			// Get item information
			$item = $this->items->getItem($itemId);
			$item->quantities = $this->quantities->getItemQuantities($itemId);

			// If there are no min/max alerts set, end here
			if($item->min_alert == null && $item->max_alert == null)
				return;

			// At this point we have alerts set.. Loop through quantities looking for
			// one that exceeds limits set
			foreach($item->quantities as $quantity) {
				if($item->min_alert != null && $quantity['quantity'] <= $item->min_alert) {
					// Minimum alert triggered! Save it and continue
					$this->alerts->triggerMinAlert($itemId, $quantity['warehouse']['id'], $quantity['quantity']);
				}else if($item->max_alert != null && $quantity['quantity'] >= $item->max_alert) {
					// Maximum alert triggered! Save it and continue
					$this->alerts->triggerMaxAlert($itemId, $quantity['warehouse']['id'], $quantity['quantity']);
				}
			}
		}
	}
}