<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section variant-3">
			<div class="header">
				<div class="title">
					<?= lang('Main.sales.new_sale') ?>
				</div>
				<div class="desc">
					<?= lang('Main.sales.new_sale_description') ?>
				</div>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.sales.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="reference" class="d-block"><?= lang('Main.sales.reference') ?>*</label>
								<div class="input-group">
									<input type="text" id="reference" name="reference" class="form-control" />
									<div class="input-group-append">
										<button type="button" onclick="generateReference()" class="btn btn-primary">
											<i class="fas fa-sync-alt"></i>
										</button>
									</div>
									<div class="invalid-feedback"></div>
								</div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">

						</div>
					</div>

					<div class="row mt-n3">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="warehouse" class="d-block"><?= lang('Main.sales.warehouse') ?>*</label>
								<select name="warehouse" id="warehouse" class="custom-select">
									<option value="" disabled selected><?= lang('Main.sales.select_warehouse') ?></option>
								</select>
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="customer" class="d-block"><?= lang('Main.sales.customer.customer') ?>*</label>
								<select name="customer" id="customer" class="custom-select">
									<option value="" disabled selected><?= lang('Main.sales.select_customer') ?></option>
								</select>
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary"><?= lang('Main.sales.add_items') ?></h6>
							<span class="autocomplete-desc mb-3">
								<?= lang('Main.sales.add_items_description') ?>
							</span>

							<div class="autocomplete-container">
								<input type="text" id="item_search" name="item_search" placeholder="<?= langSlashes('Main.sales.add_items_placeholder') ?>" autocomplete="off" class="form-control" disabled />
								<ul class="dropdown-menu" id="itemSuggestions"></ul>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<div class="table-responsive">
								<table id="items" class="table table-bordered">
									<thead>
										<tr>
											<th><?= lang('Main.sales.items.item_name') ?></th>
											<th><?= lang('Main.sales.items.unit_price') ?></th>
											<th><?= lang('Main.sales.items.quantity') ?></th>
											<th><?= lang('Main.sales.items.subtotal') ?></th>
											<th><?= lang('Main.sales.items.tax') ?></th>
											<th><?= lang('Main.sales.items.total') ?></th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="shipping_cost" class="d-block"><?= lang('Main.sales.shipping_cost') ?> (<?= $settings->currency_symbol ?>)</label>
								<input type="text" name="shipping_cost" id="shipping_cost" class="form-control" value="0" disabled />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="discount" class="d-block"><?= lang('Main.sales.discount') ?> (<?= $settings->currency_symbol ?>)</label>
								<input type="text" name="discount" id="discount" class="form-control" value="0" disabled />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="tax" class="d-block"><?= lang('Main.sales.tax') ?> (%)</label>
								<input type="text" name="tax" id="tax" class="form-control" value="0" disabled />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<div class="row mt-n3">
						<div class="col-sm-6 text-break pl-2 pr-2 mt-3">
							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.sales.notes') ?></label>
								<textarea name="notes" id="notes" class="form-control" rows="6"></textarea>
							</div>
						</div>

						<div class="mt-3 col-sm-2">
							
						</div>

						<div class="col-sm-4 text-break pl-2 pr-2 mt-3">
							<table id="summary" class="table stacked">
								<tbody>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.sales.subtotal') ?></th>
										<td width="60" data-summary-field="subtotal"><?= $settings->currency_symbol ?> 0</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.sales.discount') ?></th>
										<td width="60" data-summary-field="discount"><?= $settings->currency_symbol ?> 0</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.sales.shipping_cost') ?></th>
										<td width="60" data-summary-field="shipping"><?= $settings->currency_symbol ?> 0</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.sales.tax') ?></th>
										<td width="60" data-summary-field="tax">0%</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-bold"><?= lang('Main.sales.grand_total') ?></th>
										<td width="60" data-summary-field="total" class="font-weight-bold"><?= $settings->currency_symbol ?> 0</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.sales.create') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';
	
let currency = "<?= addslashes($settings->currency_symbol) ?>";
let itemsAdded = [];
let selectedWarehouse = 0;
let selectedCustomer = 0;

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Populate selects
		loadWarehouses()
		loadCustomers()

		// Once a warehouse and customer are selected, enable items section
		$('select[name=warehouse], select[name=customer]').on('change', e => {
			let warehouse = $('select[name=warehouse]').val()
			let customer = $('select[name=customer]').val()

			if(warehouse != '' && warehouse != null && customer != '' && customer != null) {
				selectedWarehouse = warehouse
				selectedCustomer = customer
				$('select[name=warehouse]').prop('disabled', true)
				$('select[name=customer]').prop('disabled', true)
				$('input[name=item_search]').prop('disabled', false)
			}
		})

		// When focusing on the autocomplete, show list
		$('input[name=item_search]').on('focus', e => {
			$('.autocomplete-container').addClass('open')
		})
		$('input[name=item_search]').on('blur', e => {
			// Timeout so that the item clicked listener can fire
			setTimeout(() => {
				$('.autocomplete-container').removeClass('open')
			}, 200)
		})

		// Listen for changes on the autocomplete input
		$('input[name=item_search]').on('input', e => {
			autocomplete()
		})

		// When hitting enter in the autocomplete, it's because user entered
		// an item code.. Search for it, and it if exists, load the info
		$('input[name=item_search]').on('keypress', e => {
			if(e.which == 13) {
				e.preventDefault();
				onSearchItemCode();
			}
		})

		// When selecting an item to add
		$('ul#itemSuggestions').on('click', 'li', e => {
			let id = $(e.currentTarget).data('item-id')
			addItem(id)
		})

		// To remove item
		$('table#items').on('click', 'tr td button', e => {
			let parent = $(e.currentTarget).parent().parent().parent().parent()
			let itemId = parent.data('item-id')

			parent.remove()

			let indexToRemove = -1
			itemsAdded.forEach((item, i) => {
				if(itemId == item.id)
					indexToRemove = i
			})
			itemsAdded.splice(indexToRemove, 1)

			updateTotals()
		})

		// When changing quantity of an item
		$('table#items').on('tr td input', 'input', e => {
			updateTotals()
		})

		// When changing shipping cost, discount or tax...
		$('input[name=shipping_cost], input[name=discount], input[name=tax]').on('input', e => {
			updateTotals()
		})

		$('form').on('submit', e => {
			e.preventDefault()
			createSale()
		})
	})
})(jQuery)

function onSearchItemCode() {
	let itemCode = $('input[name=item_search]').val()

	$('input[name=item_search]').blur().val('')

	axios.get(`api/items/code`, {
		params: {
			code: itemCode
		}
	}).then(response => {
		addItem(response.data.id)
	})
}

function autocomplete() {
	let search = $('input[name=item_search]').val()

	axios.get(`api/items/list`, {
		params: {
			search: search
		}
	}).then(response => {
		$('ul#itemSuggestions').empty()

		response.data.forEach(item => {
			let elem = `<li data-item-id="${item.id}">`
				+ `<span class="item-name">${item.name}</span>`
				+ `<span class="item-code">${item.code}</span>`
				+ '</li>'

			$('ul#itemSuggestions').append(elem)
		})
	})
}

function loadWarehouses() {
	axios.get(`api/warehouses/list`).then(response => {
		let elems = []

		response.data.forEach(warehouse => {
			elems += `<option value="${warehouse.id}">${warehouse.name}</option>`
		})

		$('select#warehouse').append(elems)
	})
}

function loadCustomers() {
	axios.get(`api/customers/list`).then(response => {
		let elems = []

		response.data.forEach(customer => {
			elems += `<option value="${customer.id}">${customer.name}</option>`
		})

		$('select#customer').append(elems)
	})
}

function generateReference() {
	axios.get(`api/sales/unique-reference`).then(response => {
		$('input[name=reference]').val(response.data.reference)
	})
}

function addItem(itemId) {
	// Item added already? Let the user know
	if($(`table#items tr[data-item-id="${itemId}"]`).length) {
		showError("<?= langSlashes('Errors.error') ?>", "<?= langSlashes('Errors.sales.frontend.item_already_added') ?>")
		return
	}

	// Enable rest of fields
	$('input[name=shipping_cost]').prop('disabled', false)
	$('input[name=discount]').prop('disabled', false)
	$('input[name=tax]').prop('disabled', false)

	axios.get(`api/items/${itemId}/warehouse/${selectedWarehouse}`).then(response => {
		let item = response.data

		item.sale_quantity = 0

		itemsAdded.push(item)

		let td1 = '<div class="d-flex">'
			+ '<div>'
			+ '<button type="button" class="btn item-delete btn-secondary"><i class="fas fa-trash-alt"></i></button>'
			+ '</div>'
			+ '<div>'
			+ `<strong>${item.name}</strong>`
			+ '<br />'
			+ item.code
			+ '</div>'
			+ '</div>'
		let td2 = Utils.twoDecimals(item.sale_price)
		let td3 = '<div class="input-group input-group-sm">'
				+ '<input type="text" class="form-control form-control-sm" value="0" />'
				+ '<div class="input-group-append">'
				+ `<span class="input-group-text">/${item.quantity}</span>`
				+ '</div>'
				+ '</div>'
		let td4 = Utils.twoDecimals(0)
		let td5 = Utils.twoDecimals(item.sale_tax)
		let td6 = Utils.twoDecimals(0)

		//table#items
		let elem = `<tr data-item-id="${itemId}">`
			+ `<td>${td1}</td>`
			+ `<td data-item-td="unit_price">${currency} ${td2}</td>`
			+ `<td data-item-td="quantity">${td3}</td>`
			+ `<td data-item-td="subtotal">${currency} ${td4}</td>`
			+ `<td data-item-td="tax">${td5}%</td>`
			+ `<td data-item-td="total">${currency} ${td6}</td>`
			+ '</tr>'

		$('table#items').append(elem)
	})
}

function updateTotals() {
	let subtotal = 0

	itemsAdded.forEach((item, i) => {
		let field = $(`table#items tbody tr[data-item-id=${item.id}] input`)
		let qty = Utils.getInt(field.val())

		// If quantity is greater than availble in stock, rewrite user input
		if(qty > item.quantity) {
			qty = item.quantity
			$(`table#items tbody tr[data-item-id=${item.id}] input`).val(qty)
		}

		// Update quantity in the original array
		itemsAdded[i].sale_quantity = qty

		let item_subtotal = Utils.twoDecimals(qty * Utils.getFloat(item.sale_price))
		let item_total = Utils.twoDecimals(Utils.applyTax(Utils.getFloat(item_subtotal), Utils.getFloat(item.sale_tax)))

		$(`table#items tbody tr[data-item-id=${item.id}] td[data-item-td="subtotal"]`).html(`${currency} ${item_subtotal}`)
		$(`table#items tbody tr[data-item-id=${item.id}] td[data-item-td="total"]`).html(`${currency} ${item_total}`)

		subtotal += Utils.getFloat(item_total)
	})

	let shipping_cost = Utils.getFloat($('input[name=shipping_cost]').val())
	let discount = Utils.getFloat($('input[name=discount]').val())
	let tax = Utils.getFloat($('input[name=tax]').val())

	// Cap discount to order's subtotal
	if(discount > subtotal)
		discount = subtotal

	let grand_total = subtotal
	grand_total = grand_total - discount
	grand_total = grand_total + shipping_cost
	let tax_amount = tax * grand_total / 100
	grand_total = grand_total + tax_amount

	$('table#summary tr td[data-summary-field="subtotal"]').html(`${currency} ${Utils.twoDecimals(subtotal)}`)
	$('table#summary tr td[data-summary-field="discount"]').html(`${currency} ${Utils.twoDecimals(discount)}`)
	$('table#summary tr td[data-summary-field="shipping"]').html(`${currency} ${Utils.twoDecimals(shipping_cost)}`)
	$('table#summary tr td[data-summary-field="tax"]').html(`${Utils.twoDecimals(tax)}%`)
	$('table#summary tr td[data-summary-field="total"]').html(`${currency} ${Utils.twoDecimals(grand_total)}`)
}

function createSale() {
	// Perform initial validation
	let validator = new Validator()
	validator.addInputText('reference', 'non-empty', "<?= langSlashes('Validation.sales.reference_min_length') ?>")
	validator.addSelect('warehouse', 'selected', "<?= langSlashes('Validation.sales.frontend.warehouse_not_selected') ?>")
	validator.addSelect('customer', 'selected', "<?= langSlashes('Validation.sales.frontend.customer_not_selected') ?>")
	validator.addInputText('shipping_cost', 'decimal', "<?= langSlashes('Validation.sales.shipping_cost_decimal') ?>")
	validator.addInputText('discount', 'decimal', "<?= langSlashes('Validation.sales.discount_decimal') ?>")
	validator.addInputText('tax', 'decimal', "<?= langSlashes('Validation.sales.tax_decimal') ?>")

	if(!validator.validate())
		return

	// Now make sure we have at least one item
	if($('table#items tbody tr').length == 0) {
		showError('<?= langSlashes('Errors.error') ?>', "<?= langSlashes('Errors.sales.frontend.item_not_added') ?>")
		return
	}

	// Build data object!
	let data = {
		reference: $('input[name=reference]').val(),
		customer_id: $('select[name=customer]').val(),
		warehouse_id: $('select[name=warehouse]').val(),
		shipping_cost: $('input[name=shipping_cost]').val(),
		discount: $('input[name=discount]').val(),
		discount_type: 'amount',
		tax: $('input[name=tax]').val(),
		notes: $('textarea[name=notes]').val(),
		items: []
	}

	itemsAdded.forEach(item => {
		data.items.push({
			id: item.id,
			name: item.name,
			code: item.code,
			unit_price: item.sale_price,
			quantity: item.sale_quantity
		})
	})

	axios.post(`api/sales`, data).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/sales/${response.data.id}`
	})
}
</script>
<?= $this->endSection() ?>