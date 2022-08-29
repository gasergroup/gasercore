<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;
use App\Libraries\ReferenceGenerator;

class Transfers extends BaseController {

	// Define create rules
	private $rules = [
		'from_warehouse_id' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.transfers.from_warehouse_id_required'
			]
		],
		'to_warehouse_id' => [
			'rules' => 'required',
			'errors' => [
				'required' => "Validation.transfers.to_warehouse_id_required"
			]
		],
		'items' => [
			'rules' => 'required',
			'errors' => [
				'required' => 'Validation.transfers.items_required'
			]
		],
		'notes' => [
			'rules' => 'permit_empty'
		]
	];

	public function __construct() {
		
	}


	/**
	 * To get all transfers
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function index() {
		$columns = ['from_warehouse_name', 'to_warehouse_name', 'created_by', 'created_at'];
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

		$this->transfers->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->transfers->dtGetAllTransfers(true, $warehouseIds);
		}else{
			$result = $this->transfers->dtGetAllTransfers();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}


	/**
	 * To get a single transfer by ID
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show($id) {
		$transfer = $this->transfers->getTransfer($id);

		if(!$transfer)
			return $this->failNotFound(lang('Errors.transfers.not_found', ['id' => $id]));

		// If user is supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $transfer->warehouse->id))
				return $this->failUnauthorized(lang('Errors.transfers.warehouse_unauthorized'));
		}

		return $this->respond($transfer);
	}


	/**
	 * To create a new transfer
	 * 
	 * Method			POST
	 * Filter			auth:admin
	*/
	public function create() {
		if(!$this->validateRequestWithRules($this->rules))
			return $this->failWithValidationErrors();

		$createFields = [
			'from_warehouse_id',
			'to_warehouse_id',
			'items',
			'notes'
		];

		$data = $this->buildCreateArray($createFields, true);

		// Make sure FROM and TO warehouses are different
		if($data['from_warehouse_id'] == $data['to_warehouse_id'])
			return $this->fail(lang('Errors.transfers.same_warehouse_id'));

		// Make sure both warehouse IDs exist
		if(!$this->warehouses->getWarehouse($data['from_warehouse_id']))
			return $this->failNotFound(lang('Errors.transfers.from_warehouse_not_found'));
		if(!$this->warehouses->getWarehouse($data['to_warehouse_id']))
			return $this->failNotFound(lang('Errors.transfers.to_warehouse_not_found'));

		// Validate items
		if(!is_array($data['items']) || count($data['items']) == 0)
			return $this->failValidationErrors(lang('Errors.transfers.items.malformed'));

		$itemsArr = $data['items'];

		/* Now we'll loop through the items, making sure it's well formed and:
			- All items exist (in the FROM warehouse)
			- Info matches (id, name)
			- We have enough items in stock for subtraction
		*/
		for($i = 0; $i < count($data['items']); $i++) {
			// First, make sure all properties exist
			$itemProperties = array_keys($data['items'][$i]);
			$requiredProperties = [
				'id',
				'name',
				'code',
				'original_from_quantity',
				'original_to_quantity',
				'transfer_quantity'
			];

			if(count(array_diff($itemProperties, $requiredProperties)) > 0)
				return $this->failValidationErrors(lang('Errors.transfers.items.malformed'));

			// Convert to object to work better
			$itemObj = (object) $data['items'][$i];

			// Make sure item exists
			$item = $this->items->getItemWithWarehouse($itemObj->id, $data['from_warehouse_id']);
			$itemToWarehouse = $this->items->getItemWithWarehouse($itemObj->id, $data['to_warehouse_id']);
			if(!$item)
				return $this->failNotFound(lang('Errors.transfers.items.not_found', ['id' => $itemObj->id]));

			// Make sure item info matches
			if($itemObj->name != $item->name
				|| $itemObj->code != $item->code
				|| $itemObj->original_from_quantity != $item->quantity
				|| $itemObj->original_to_quantity != $itemToWarehouse->quantity)
				return $this->fail(lang('Errors.transfers.items.inconsistent'));

			// Make sure transfer is numeric
			if(!$this->validation->check($itemObj->transfer_quantity, 'numeric'))
				return $this->fail(lang('Validation.transfers.transfer_quantity_numeric'));

			// Make sure we've got enough items in stock to subtract from warehouse
			if($itemObj->transfer_quantity > $item->quantity)
				return $this->fail(lang('Errors.transfers.not_enough_qty'));
		}

		// Make sure there are no duplicate items
		// For this, we'll extract the IDs, and then remove duplicates and compare number of
		// items
		$item_ids = [];
		foreach($data['items'] as $itemArr)
			$item_ids[] = $itemArr['id'];
		if(count(array_unique($item_ids)) < count($data['items']))
			return $this->fail(lang('Validation.transfers.duplicate_items'));

		$data['created_by'] = $this->logged_user->id;
		$data['items'] = json_encode($data['items']);

		// Insert transfer
		$transfer_id = $this->transfers->insert($data);

		// Now let's update quantities in stock
		// We'll subtract quantities from "From warehouse", and add them to "To warehouse"
		foreach($itemsArr as $itemArr) {
			$this->quantities->removeStock($itemArr['transfer_quantity'], $itemArr['id'], $data['from_warehouse_id']);
			$this->quantities->addStock($itemArr['transfer_quantity'], $itemArr['id'], $data['to_warehouse_id']);
		}

		// Finally update quantity alerts
		$itemIds = [];
		foreach($itemsArr as $itemArr)
			$itemIds[] = $itemArr['id'];

		$this->updateAlerts($itemIds);

		// Done!
		$new_transfer = $this->transfers->getTransfer($transfer_id);

		// Done!
		return $this->respondCreated($new_transfer);
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