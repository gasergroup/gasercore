<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;
use App\Libraries\ReferenceGenerator;

class Purchases extends BaseController {

	// Define create and update rules
	private $rules = [
		'create_purchase' => [
			'reference' => [
				'rules' => 'min_length[1]|max_length[45]',
				'errors' => [
					'min_length' => 'Validation.purchases.reference_min_length',
					'max_length' => 'Validation.purchases.reference_max_length'
				]
			],
			'supplier_id' => [
				'rules' => 'numeric',
				'errors' => [
					'numeric' => 'Validation.purchases.supplier_id_numeric'
				]
			],
			'warehouse_id' => [
				'rules' => 'numeric',
				'errors' => [
					'numeric' => 'Validation.purchases.warehouse_id_numeric'
				]
			],
			'items' => [
				'rules' => 'required',
				'errors' => [
					'required' => 'Validation.purchases.items_required'
				]
			],
			'shipping_cost' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.purchases.shipping_cost_decimal'
				]
			],
			'discount' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.purchases.discount_decimal'
				]
			],
			'discount_type' => [
				'rules' => 'in_list[percentage,amount]',
				'errors' => [
					'in_list' => 'Validation.purchases.discount_type_invalid'
				]
			],
			'tax' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.purchases.tax_decimal'
				]
			],
			'notes' => [
				'rules' => 'permit_empty'
			]
		],

		'create_return' => [
			'items' => [
				'rules' => 'required',
				'errors' => [
					'required' => 'Validation.purchases.items_required'
				]
			],
			'shipping_cost' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.purchases.shipping_cost_decimal'
				]
			],
			'discount' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.purchases.discount_decimal'
				]
			],
			'tax' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.purchases.tax_decimal'
				]
			],
			'notes' => [
				'rules' => 'permit_empty'
			]
		]
	];

	public function __construct() {
		$this->rules = (object) $this->rules;
	}

	/**
	 * To get all purchases
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function index() {
		$columns = ['reference', 'warehouse_name', 'created_at', 'supplier_name', 'grand_total'];
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

		$this->purchases->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user worker/supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->purchases->dtGetAllPurchases(true, $warehouseIds);
		}else{
			$result = $this->purchases->dtGetAllPurchases();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To get a single purchase by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show($id) {
		$purchase = $this->purchases->getPurchase($id);

		if(!$purchase)
			return $this->failNotFound(lang('Errors.purchases.not_found', ['id' => $id]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $purchase->warehouse->id))
				return $this->failUnauthorized(lang('Errors.purchases.warehouse_unauthorized'));
		}

		return $this->respond($purchase);
	}

	/**
	 * To create a purchase return
	 * 
	 * Method			POST
	 * Filter			auth
	 */
	public function return($purchaseId) {
		if(!$this->validateRequestWithRules($this->rules->create_return))
			return $this->failWithValidationErrors();

		$createFields = [
			'items',
			'shipping_cost',
			'discount',
			'tax',
			'notes'
		];

		$data = $this->buildCreateArray($createFields, true);

		// Make sure purchase does exist
		$purchase = $this->purchases->getPurchase($purchaseId);
		if(!$purchase)
			return $this->failNotFound(lang('Errors.purchases.not_found', ['id' => $purchaseId]));

		// Make sure purchase doesn't have any returns yet
		if($this->purchases_returns->getPurchaseReturn($purchaseId))
			return $this->failResourceExists(lang('Errors.purchases.returns.already_exists'));

		// Generate return reference
		$references_purchase_return_prepend = $this->settings->getSetting('references_purchase_return_prepend');
		$references_purchase_return_append = $this->settings->getSetting('references_purchase_return_append');
		$data['reference'] = "{$references_purchase_return_prepend}{$purchase->reference}{$references_purchase_return_append}";

		// Make sure reference doesn't exist
		if($this->doesReferenceExist($data['reference']))
			return $this->failResourceExists(lang('Errors.purchases.already_exists_reference'));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $purchase['warehouse_id']))
				return $this->failUnauthorized(lang('Errors.purchases.warehouse_unauthorized'));
		}

		// Validate items
		if(!is_array($data['items']) || count($data['items']) == 0)
			return $this->failValidationErrors(lang('Errors.purchases.items.malformed'));

		$itemsArr = $data['items'];

		// Re-organize purchase items... [id => {info...}]
		$purchase_items = [];
		foreach($purchase->items AS $item)
			$purchase_items[$item->id] = $item;

		// Make sure number of items in the purchase match the number of items
		// in the return
		if(count($purchase_items) != count($data['items']))
			return $this->failValidationErrors(lang('Errors.purchases.items.malformed'));

		// Now, we'll loop through the items array sent as part of the return,
		// making sure it's well formed and validating info.
		$atLeastOneReturn = false;
		foreach($data['items'] as $itemObj) {
			// Make sure all properties exist
			$itemProperties = array_keys($itemObj);

			$requiredProperties = ['id', 'qty_to_return'];

			if(count(array_diff($itemProperties, $requiredProperties)) > 0)
				return $this->failValidationErrors(lang('Errors.purchases.items.malformed'));

			// Convert to object to work better
			$itemObj = (object) $itemObj;

			// Make sure this ID matches with an ID of one of the items in the original purchase
			if(!isset($purchase_items[$itemObj->id]))
				return $this->failValidationErrors(lang('Errors.purchases.returns.unexisting_id'));

			// Make sure qty_to_return is numeric
			if(!$this->validation->check($itemObj->qty_to_return, 'numeric'))
				return $this->fail(lang('Validation.purchases.returns.item_quantity_numeric'));

			// Are we returning at least one item? Update flag
			if($itemObj->qty_to_return > 0)
				$atLeastOneReturn = true;

			// Make sure user isn't trying to return more items than originally purchased
			if($itemObj->qty_to_return > $purchase_items[$itemObj->id]->quantity)
				return $this->fail(lang('Errors.purchases.returns.exceeding_qty'));

			// Make sure we've got enough items in stock to return
			if($itemObj->qty_to_return > $this->quantities->getItemQuantity($itemObj->id, $purchase->warehouse->id))
				return $this->fail(lang('Errors.purchases.returns.not_enough_qty'));
		}

		// Make sure we're returning at least a single item
		if(!$atLeastOneReturn)
			return $this->fail(lang('Errors.purchases.returns.not_returning'));

		// Calculate return order's subtotal
		$data['subtotal'] = 0;
		foreach($data['items'] as $itemArr) {
			$purchaseItem = $purchase_items[$itemArr['id']];
			$itemSubtotal = round($purchaseItem->unit_price * $itemArr['qty_to_return'], 2);
			$itemTax = round($itemSubtotal * $purchaseItem->tax / 100, 2);
			$itemTotal = round($itemSubtotal + $itemTax, 2);

			$data['subtotal'] = round($data['subtotal'] + $itemTotal, 2);
		}
		
		// Make sure discount (amount) doesn't exceed order's subtotal
		// Since this is a return, subtotal is the amount of money that should
		// be given back to us, and discount will be subtracted from that
		if($data['discount'] > $data['subtotal'])
			return $this->fail(lang('Validation.purchases.returns.discount_amount_greater_than'));

		/* To calculate total we'll do this to the subtotal:
			- Subtract discount
			- Add shipping cost
			- Add tax
		*/
		$data['grand_total'] = $data['subtotal'];
		$data['grand_total'] = round($data['grand_total'] - $data['discount'], 2);
		$data['grand_total'] = round($data['grand_total'] + $data['shipping_cost'], 2);
		$tax = round($data['tax'] * $data['grand_total'] / 100, 2);
		$data['grand_total'] = round($data['grand_total'] + $tax, 2);

		$data['purchase_id'] = $purchaseId;
		$data['created_by'] = $this->logged_user->id;
		$data['items'] = json_encode($data['items']);

		// Insert return
		$return_id = $this->purchases_returns->insert($data);

		// Now, update quantities in stock
		foreach($itemsArr as $itemArr)
			$this->quantities->removeStock($itemArr['qty_to_return'], $itemArr['id'], $purchase->warehouse->id);

		// Finally update quantity alerts
		$itemIds = [];
		foreach($itemsArr as $itemArr)
			$itemIds[] = $itemArr['id'];

		$this->updateAlerts($itemIds);

		// Done!
		$new_return = $this->purchases_returns->getReturn($return_id);

		return $this->respondCreated($new_return);
	}

	/**
	 * To create a new purchase
	 * 
	 * Method			POST
	 * Filter			auth
	 */
	public function create() {
		if(!$this->validateRequestWithRules($this->rules->create_purchase))
			return $this->failWithValidationErrors();

		$createFields = [
			'reference',
			'supplier_id',
			'warehouse_id',
			'items',
			'shipping_cost',
			'discount',
			'discount_type',
			'tax',
			'notes'
		];

		$data = $this->buildCreateArray($createFields, true);

		// Make sure reference doesn't exist
		if($this->doesReferenceExist($data['reference']))
			return $this->failResourceExists(lang('Errors.purchases.already_exists_reference'));

		// Make sure supplier ID exists
		if(!$this->suppliers->getSupplier($data['supplier_id']))
			return $this->failNotFound(lang('Errors.purchases.supplier_not_found'));
		
		// Make sure warehouse ID exists
		if(!$this->warehouses->getWarehouse($data['warehouse_id']))
			return $this->failNotFound(lang('Errors.purchases.warehouse_not_found'));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $data['warehouse_id']))
				return $this->failUnauthorized(lang('Errors.purchases.warehouse_unauthorized'));
		}

		// Validate items
		if(!is_array($data['items']) || count($data['items']) == 0)
			return $this->failValidationErrors(lang('Errors.purchases.items.malformed'));

		$itemsArr = $data['items'];

		/* Now we'll loop through the items, making sure it's well formed and:
			- All items exist
			- All items have this supplier
			- All info matches (name, price, etc)
		*/
		for($i = 0; $i < count($data['items']); $i++) {
			// First, make sure all properties exist
			$itemProperties = array_keys($data['items'][$i]);
			$requiredProperties = ['id', 'name', 'code', 'unit_price', 'quantity'];

			if(count(array_diff($itemProperties, $requiredProperties)) > 0)
				return $this->failValidationErrors(lang('Errors.purchases.items.malformed'));

			// Convert to item to work better
			$itemObj = (object) $data['items'][$i];

			// Make sure item exists
			$item = $this->items->getItem($itemObj->id);
			if(!$item)
				return $this->failNotFound(lang('Errors.purchases.items.not_found', ['id' => $itemObj->id]));

			// Make sure supplier is registered to this item
			$supplier_relation = $this->item_suppliers->getItemSupplier($item->id, $data['supplier_id']);
			if(!$supplier_relation)
				return $this->fail(lang('Errors.purchases.items.item_supplier_not_found', ['item_id' => $item->id, 'supplier_id' => $data['supplier_id']]));

			// Make sure item info matches
			if($itemObj->name != $item->name
				|| $itemObj->code != $item->code
				|| $itemObj->unit_price != $supplier_relation->price)
				return $this->fail(lang('Errors.purchases.items.inconsistent'));

			// Make sure quantity is numeric
			if(!$this->validation->check($itemObj->quantity, 'numeric'))
				return $this->fail(lang('Validation.purchases.item_quantity_numeric'));

			// Load tax from our item-supplier relation
			$data['items'][$i]['tax'] = $supplier_relation->tax;
		}

		// Make sure there are no duplicate items
		// For this, we'll extract the IDs, and then remove duplicates and compare number of
		// items
		$item_ids = [];
		foreach($data['items'] as $itemArr)
			$item_ids[] = $itemArr['id'];
		if(count(array_unique($item_ids)) < count($data['items']))
			return $this->fail(lang('Validation.purchases.duplicate_items'));

		// Calculate order's subtotal
		$data['subtotal'] = 0;
		foreach($data['items'] as $itemArr) {
			$itemSubtotal = round($itemArr['unit_price'] * $itemArr['quantity'], 2);
			$itemTax = round($itemSubtotal * $itemArr['tax'] / 100, 2);
			$itemTotal = round($itemSubtotal + $itemTax, 2);

			$data['subtotal'] = round($data['subtotal'] + $itemTotal, 2);
		}

		// If discount is percentage, make sure it doesn't exceed 100%
		// If it's amount, make sure it doesn't exceed order's subtotal
		if($data['discount_type'] == 'percentage' && $data['discount'] > 100)
			return $this->fail(lang('Validation.purchases.discount_percentage_greater_than'));
		else if($data['discount_type'] == 'amount' && $data['discount'] > $data['subtotal'])
			return $this->fail(lang('Validation.purchases.discount_amount_greater_than'));

		/* To calculate total we'll do this to the subtotal:
			- Subtract discount
			- Add shipping cost
			- Add tax
		*/
		$discount = $data['discount'];
		if($data['discount_type'] == 'percentage')
			$discount = round($data['subtotal'] * $data['discount'] / 100, 2);

		$data['grand_total'] = $data['subtotal'];
		$data['grand_total'] = round($data['grand_total'] - $discount, 2);
		$data['grand_total'] = round($data['grand_total'] + $data['shipping_cost'], 2);
		$tax = round($data['tax'] * $data['grand_total'] / 100, 2);
		$data['grand_total'] = round($data['grand_total'] + $tax, 2);

		$data['created_by'] = $this->logged_user->id;
		$data['items'] = json_encode($data['items']);

		// Insert purchase
		$purchase_id = $this->purchases->insert($data);

		// Now, let's update quantities in stock
		foreach($itemsArr as $itemArr)
			$this->quantities->addStock($itemArr['quantity'], $itemArr['id'], $data['warehouse_id']);

		// Finally update quantity alerts
		$itemIds = [];
		foreach($itemsArr as $itemArr)
			$itemIds[] = $itemArr['id'];

		$this->updateAlerts($itemIds);

		// Done!
		$new_purchase = $this->purchases->getPurchase($purchase_id);

		return $this->respondCreated($new_purchase);
	}

	/**
	 * To get a single purchase by reference
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_by_reference() {
		$reference = $this->request->getVar('reference') ?? '';

		$purchase = $this->purchases->getPurchaseByReference($reference);

		if(!$purchase)
			return $this->failNotFound(lang('Errors.purchases.not_found_with_reference', ['reference' => $reference]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $purchase->warehouse->id))
				return $this->failUnauthorized(lang('Errors.purchases.warehouse_unauthorized'));
		}

		return $this->respond($purchase);
	}

	/**
	 * To generate a unique purchase reference
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function generate_unique_reference() {
		$generator = new ReferenceGenerator();

		return $this->respond([
			'reference' => $generator->generate('purchase')
		]);
	}

	/**
	 * To get all purchase returns
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_returns() {
		$columns = [
			'reference',
			'purchase_reference',
			'warehouse_name',
			'created_at',
			'supplier_name',
			'grand_total'
		];

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

		$this->purchases_returns->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user worker/supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->purchases_returns->dtGetAllReturns(true, $warehouseIds);
		}else{
			$result = $this->purchases_returns->dtGetAllReturns();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To get a single purchase return by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_return($id) {
		$return = $this->purchases_returns->getReturn($id);

		if(!$return)
			return $this->failNotFound(lang('Errors.purchases.returns.not_found', ['id' => $id]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $return->warehouse->id))
				return $this->failUnauthorized(lang('Errors.purchases.warehouse_unauthorized'));
		}

		return $this->respond($return);
	}

	/**
	 * To get a single purchase return by purchase ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_return_by_purchase($purchase_id) {
		$return = $this->purchases_returns->getPurchaseReturn($purchase_id);

		if(!$return)
			return $this->failNotFound(lang('Errors.purchases.returns.not_found_with_purchase', ['id' => $purchase_id]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $return->warehouse->id))
				return $this->failUnauthorized(lang('Errors.purchases.warehouse_unauthorized'));
		}

		return $this->respond($return);
	}

	/**
	 * To get latest table -- Table with the 5 most recent purchases
	 * No DataTables features will be allowed
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show_latest_table() {
		// If user is supervisor, get only records from warehouses that the supervisor has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->purchases->dtGetLatest(true, $warehouseIds);
		}else{
			$result = $this->purchases->dtGetLatest();
		}

		$draw = $this->request->getVar('draw') ?? false;

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To export a CSV file with all purchases (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of purchases, with as much information as we can get
		$purchases = $this->purchases->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "purchases__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($purchases, $filename));
	}

	/**
	 * To export a CSV file with all purchase returns
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export_returns() {
		// Get list of returns, with as much information as we can get
		$returns = $this->purchases_returns->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "purchases_returns__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($returns, $filename));
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