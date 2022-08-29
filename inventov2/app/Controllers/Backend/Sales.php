<?php namespace App\Controllers\Backend;

use App\Libraries\DataTables;
use App\Libraries\ReferenceGenerator;

class Sales extends BaseController {

	// Define create and update rules
	private $rules = [
		'create_sale' => [
			'reference' => [
				'rules' => 'min_length[1]|max_length[45]',
				'errors' => [
					'min_length' => 'Validation.sales.reference_min_length',
					'max_length' => 'Validation.sales.reference_max_length'
				]
			],
			'customer_id' => [
				'rules' => 'numeric',
				'errors' => [
					'numeric' => 'Validation.sales.customer_id_numeric'
				]
			],
			'warehouse_id' => [
				'rules' => 'numeric',
				'errors' => [
					'numeric' => 'Validation.sales.warehouse_id_numeric'
				]
			],
			'items' => [
				'rules' => 'required',
				'errors' => [
					'required' => 'Validation.sales.items_required'
				]
			],
			'shipping_cost' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.sales.shipping_cost_decimal'
				]
			],
			'discount' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.sales.discount_decimal'
				]
			],
			'discount_type' => [
				'rules' => 'in_list[percentage,amount]',
				'errors' => [
					'in_list' => 'Validation.sales.discount_type_invalid'
				]
			],
			'tax' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.sales.tax_decimal'
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
					'required' => 'Validation.sales.items_required'
				]
			],
			'shipping_cost' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.sales.shipping_cost_decimal'
				]
			],
			'discount' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.sales.discount_decimal'
				]
			],
			'tax' => [
				'rules' => 'decimal',
				'errors' => [
					'decimal' => 'Validation.sales.tax_decimal'
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
	 * To get all sales
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function index() {
		$columns = ['reference', 'warehouse_name', 'created_at', 'customer_name', 'grand_total'];
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

		$this->sales->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user worker/supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->sales->dtGetAllSales(true, $warehouseIds);
		}else{
			$result = $this->sales->dtGetAllSales();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To get a single sale by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show($id) {
		$sale = $this->sales->getSale($id);

		if(!$sale)
			return $this->failNotFound(lang('Errors.sales.not_found', ['id' => $id]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $sale->warehouse->id))
				return $this->failUnauthorized(lang('Errors.sales.warehouse_unauthorized'));
		}

		return $this->respond($sale);
	}

	/**
	 * To create a sale return
	 * 
	 * Method			POST
	 * Filter			auth
	 */
	public function return($saleId) {
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

		// Make sure sale does exist
		$sale = $this->sales->getSale($saleId);
		if(!$sale)
			return $this->failNotFound(lang('Errors.sales.not_found', ['id' => $saleId]));

		// Make sure sale doesn't have any returns yet
		if($this->sales_returns->getSaleReturn($saleId))
			return $this->failResourceExists(lang('Errors.sales.returns.already_exists'));

		// Generate return reference
		$references_sale_return_prepend = $this->settings->getSetting('references_sale_return_prepend');
		$references_sale_return_append = $this->settings->getSetting('references_sale_return_append');
		$data['reference'] = "{$references_sale_return_prepend}{$sale->reference}{$references_sale_return_append}";

		// Make sure reference doesn't exist
		if($this->doesReferenceExist($data['reference']))
			return $this->failResourceExists(lang('Errors.sales.already_exists_reference'));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $sale['warehouse_id']))
				return $this->failUnauthorized(lang('Errors.sales.warehouse_unauthorized'));
		}

		// Validate items
		if(!is_array($data['items']) || count($data['items']) == 0)
			return $this->failValidationErrors(lang('Errors.sales.items.malformed'));

		$itemsArr = $data['items'];

		// Re-organize sale items... [id => {info...}]
		$sale_items = [];
		foreach($sale->items AS $item)
			$sale_items[$item->id] = $item;

		// Make sure number of items in the sale match the number of items in
		// the return
		if(count($sale_items) != count($data['items']))
			return $this->failValidationErrors(lang('Errors.sales.items.malformed'));

		// Now, we'll loop through the items array sent as part of the return,
		// making sure it's well formed and validating info.
		$atLeastOneReturn = false;
		foreach($data['items'] as $itemObj) {
			// Make sure all properties exist
			$itemProperties = array_keys($itemObj);

			$requiredProperties = ['id', 'qty_to_return'];

			if(count(array_diff($itemProperties, $requiredProperties)) > 0)
				return $this->failValidationErrors(lang('Errors.sales.items.malformed'));

			// Convert to object to work better
			$itemObj = (object) $itemObj;

			// Make sure this ID matches with an ID of one of the items in the original sale
			if(!isset($sale_items[$itemObj->id]))
				return $this->failValidationErrors(lang('Errors.sales.returns.unexisting_id'));

			// Make sure qty_to_return is numeric
			if(!$this->validation->check($itemObj->qty_to_return, 'numeric'))
				return $this->fail(lang('Validation.sales.returns.item_quantity_numeric'));

			// Are we returning at least one item? Update flag
			if($itemObj->qty_to_return > 0)
				$atLeastOneReturn = true;

			// Make sure user isn't trying to return more items than originally sold
			if($itemObj->qty_to_return > $sale_items[$itemObj->id]->quantity)
				return $this->fail(lang('Errors.sales.returns.exceeding_qty'));
		}

		// Make sure we're returning at least a single item
		if(!$atLeastOneReturn)
			return $this->fail(lang('Errors.sales.returns.not_returning'));

		// Calculate return order's subtotal
		$data['subtotal'] = 0;
		foreach($data['items'] as $itemArr) {
			$saleItem = $sale_items[$itemArr['id']];
			$itemSubtotal = round($saleItem->unit_price * $itemArr['qty_to_return'], 2);
			$itemTax = round($itemSubtotal * $saleItem->tax / 100, 2);
			$itemTotal = round($itemSubtotal + $itemTax, 2);

			$data['subtotal'] = round($data['subtotal'] + $itemTotal, 2);
		}
		
		// Make sure discount (amount) doesn't exceed order's subtotal
		// Since this is a return, subtotal is the amount of money that should
		// be given back to the customer, and discount will be subtracted from that
		if($data['discount'] > $data['subtotal'])
			return $this->fail(lang('Validation.sales.returns.discount_amount_greater_than'));

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

		$data['sale_id'] = $saleId;
		$data['created_by'] = $this->logged_user->id;
		$data['items'] = json_encode($data['items']);

		// Insert return
		$return_id = $this->sales_returns->insert($data);

		// Now, update quantities in stock
		foreach($itemsArr as $itemArr)
			$this->quantities->addStock($itemArr['qty_to_return'], $itemArr['id'], $sale->warehouse->id);

		// Finally update quantity alerts
		$itemIds = [];
		foreach($itemsArr as $itemArr)
			$itemIds[] = $itemArr['id'];

		$this->updateAlerts($itemIds);

		// Done!
		$new_return = $this->sales_returns->getReturn($return_id);

		return $this->respondCreated($new_return);
	}

	/**
	 * To create a new sale
	 * 
	 * Method			POST
	 * Filter			auth
	 */
	public function create() {
		if(!$this->validateRequestWithRules($this->rules->create_sale))
			return $this->failWithValidationErrors();

		$createFields = [
			'reference',
			'customer_id',
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
			return $this->failResourceExists(lang('Errors.sales.already_exists_reference'));

		// Make sure customer ID exists
		if(!$this->customers->getCustomer($data['customer_id']))
			return $this->failNotFound(lang('Errors.sales.customer_not_found'));
		
		// Make sure warehouse ID exists
		if(!$this->warehouses->getWarehouse($data['warehouse_id']))
			return $this->failNotFound(lang('Errors.sales.warehouse_not_found'));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $data['warehouse_id']))
				return $this->failUnauthorized(lang('Errors.sales.warehouse_unauthorized'));
		}

		// Validate items
		if(!is_array($data['items']) || count($data['items']) == 0)
			return $this->failValidationErrors(lang('Errors.sales.items.malformed'));

		$itemsArr = $data['items'];

		/* Now we'll loop through the items, making sure it's well formed and:
			- All items exist
			- All info matches (name, price, etc)
			- We have enough items in stock
		*/
		for($i = 0; $i < count($data['items']); $i++) {
			// First, make sure all properties exist
			$itemProperties = array_keys($data['items'][$i]);
			$requiredProperties = ['id', 'name', 'code', 'unit_price', 'quantity'];

			if(count(array_diff($itemProperties, $requiredProperties)) > 0)
				return $this->failValidationErrors(lang('Errors.sales.items.malformed'));

			// Convert to item to work better
			$itemObj = (object) $data['items'][$i];

			// Make sure item exists
			$item = $this->items->getItem($itemObj->id);
			if(!$item)
				return $this->failNotFound(lang('Errors.sales.items.not_found', ['id' => $itemObj->id]));

			// Make sure item info matches
			if($itemObj->name != $item->name
				|| $itemObj->code != $item->code
				|| $itemObj->unit_price != $item->sale_price)
				return $this->fail(lang('Errors.sales.items.inconsistent'));

			// Make sure quantity is numeric
			if(!$this->validation->check($itemObj->quantity, 'numeric'))
				return $this->fail(lang('Validation.sales.item_quantity_numeric'));

			// Make sure we've got enough items in stock
			if($itemObj->quantity > $this->quantities->getItemQuantity($itemObj->id, $data['warehouse_id']))
				return $this->fail(lang('Errors.sales.not_enough_qty'));

			// Save item tax (sale)
			$data['items'][$i]['tax'] = $item->sale_tax;
		}

		// Make sure there are no duplicate items
		// For this, we'll extract the IDs, and then remove duplicates and compare number of
		// items
		$item_ids = [];
		foreach($data['items'] as $itemArr)
			$item_ids[] = $itemArr['id'];
		if(count(array_unique($item_ids)) < count($data['items']))
			return $this->fail(lang('Validation.sales.duplicate_items'));

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
			return $this->fail(lang('Validation.sales.discount_percentage_greater_than'));
		else if($data['discount_type'] == 'amount' && $data['discount'] > $data['subtotal'])
			return $this->fail(lang('Validation.sales.discount_amount_greater_than'));

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

		// Insert sale
		$sale_id = $this->sales->insert($data);

		// Now, let's update quantities in stock
		foreach($itemsArr as $itemArr)
			$this->quantities->removeStock($itemArr['quantity'], $itemArr['id'], $data['warehouse_id']);

		// Finally update quantity alerts
		$itemIds = [];
		foreach($itemsArr as $itemArr)
			$itemIds[] = $itemArr['id'];

		$this->updateAlerts($itemIds);

		// Done!
		$new_sale = $this->sales->getSale($sale_id);

		return $this->respondCreated($new_sale);
	}

	/**
	 * To get a single sale by reference
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_by_reference() {
		$reference = $this->request->getVar('reference') ?? '';

		$sale = $this->sales->getSaleByReference($reference);

		if(!$sale)
			return $this->failNotFound(lang('Errors.sales.not_found_with_reference', ['reference' => $reference]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $sale->warehouse->id))
				return $this->failUnauthorized(lang('Errors.sales.warehouse_unauthorized'));
		}

		return $this->respond($sale);
	}

	/**
	 * To generate a unique sale reference
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function generate_unique_reference() {
		$generator = new ReferenceGenerator();

		return $this->respond([
			'reference' => $generator->generate('sale')
		]);
	}

	/**
	 * To get all sale returns
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_returns() {
		$columns = [
			'reference',
			'sale_reference',
			'warehouse_name',
			'created_at',
			'customer_name',
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

		$this->sales_returns->setDtParameters($search, $orderBy, $orderDir, $length, $start);

		// Is user worker/supervisor? Let's limit by the warehouses he has access to
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->sales_returns->dtGetAllReturns(true, $warehouseIds);
		}else{
			$result = $this->sales_returns->dtGetAllReturns();
		}

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To get a single sale return by ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_return($id) {
		$return = $this->sales_returns->getReturn($id);

		if(!$return)
			return $this->failNotFound(lang('Errors.sales.returns.not_found', ['id' => $id]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $return->warehouse->id))
				return $this->failUnauthorized(lang('Errors.sales.warehouse_unauthorized'));
		}

		return $this->respond($return);
	}

	/**
	 * To get a single sale return by sale ID
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function show_return_by_sale($sale_id) {
		$return = $this->sales_returns->getSaleReturn($sale_id);

		if(!$return)
			return $this->failNotFound(lang('Errors.sales.returns.not_found_with_sale', ['id' => $sale_id]));

		// If user is worker/supervisor, make sure he has access to this warehouse
		if($this->logged_user->role == 'worker' || $this->logged_user->role == 'supervisor') {
			if(!$this->warehouse_relations->findRelation($this->logged_user->id, $return->warehouse->id))
				return $this->failUnauthorized(lang('Errors.sales.warehouse_unauthorized'));
		}

		return $this->respond($return);
	}

	/**
	 * To get latest table -- Table with the 5 most recent sales
	 * No DataTables features will be allowed
	 * 
	 * Method			GET
	 * Filter			auth:supervisor,admin
	 */
	public function show_latest_table() {
		// If user is supervisor, get only records from warehouses that the supervisor has access to
		if($this->logged_user->role == 'supervisor') {
			$warehouseIds = $this->warehouse_relations->getWarehouseIdsByUser($this->logged_user->id);
			$result = $this->sales->dtGetLatest(true, $warehouseIds);
		}else{
			$result = $this->sales->dtGetLatest();
		}

		$draw = $this->request->getVar('draw') ?? false;

		return $this->respond(array_merge(
			['draw' => $draw],
			$result
		));
	}

	/**
	 * To export a CSV file with all sales (admins only)
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export() {
		// Get list of sales, with as much information as we can get
		$sales = $this->sales->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "sales__{$filename}.csv";

		helper('csv');

		die(offer_csv_download($sales, $filename));
	}

	/**
	 * To export a CSV file with all sale returns
	 * 
	 * Method			GET
	 * Filter			auth
	 */
	public function export_returns() {
		// Get list of returns, with as much information as we can get
		$returns = $this->sales_returns->getDetailedList();

		// Create a filename and export!
		$filename = date('Y_m_d__H_i_s');
		$filename = "sales_returns__{$filename}.csv";

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