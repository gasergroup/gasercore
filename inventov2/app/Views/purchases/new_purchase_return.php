xdasdsad<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>
<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section variant-3">
			<div class="header">
				<div class="title">
					<?= lang('Main.purchases_returns.new_purchase_return') ?>
				</div>
				<div class="desc">
					<?= lang('Main.purchases_returns.new_purchase_return_description') ?>
				</div>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary"><?= lang('Main.purchases_returns.purchase_reference') ?></h6>
							<span class="autocomplete-desc mb-3">
								<?= lang('Main.purchases_returns.purchase_reference_description') ?>
							</span>

							<div class="form-group mb-0">
								<input type="text" id="purchase_reference" name="purchase_reference" placeholder="<?= langSlashes('Main.purchases_returns.purchase_reference') ?>" autocomplete="off" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="reference" class="d-block"><?= lang('Main.purchases_returns.return_reference_generated') ?></label>
								<input type="text" name="reference" id="reference" class="form-control" disabled />
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="date" class="d-block"><?= lang('Main.purchases_returns.date') ?></label>
								<input type="text" name="date" id="date" class="form-control" disabled />
							</div>
						</div>
					</div>

					<div class="row mt-n3">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="warehouse" class="d-block"><?= lang('Main.purchases_returns.warehouse') ?></label>
								<input type="text" name="warehouse" id="warehouse" class="form-control" disabled />
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="supplier" class="d-block"><?= lang('Main.purchases_returns.supplier.supplier') ?></label>
								<input type="text" name="supplier" id="supplier" class="form-control" disabled />
							</div>
						</div>
					</div>

					<div class="row mt-0">
						<div class="col-sm text-break pl-2 pr-2">
							<span class="text-secondary autocomplete-desc">
								<?= lang('Main.purchases_returns.quantity_instructions') ?>
							</span>				
						</div>
					</div>

					<div class="row mt-4">
						<div class="col-sm text-break pl-2 pr-2">
							<div class="table-responsive">
								<table id="items" class="table table-bordered">
									<thead>
										<tr>
											<th><?= lang('Main.purchases_returns.items.item_name') ?></th>
											<th><?= lang('Main.purchases_returns.items.unit_price') ?></th>
											<th><?= lang('Main.purchases_returns.items.quantity') ?></th>
											<th><?= lang('Main.purchases_returns.items.subtotal') ?></th>
											<th><?= lang('Main.purchases_returns.items.tax') ?></th>
											<th><?= lang('Main.purchases_returns.items.total') ?></th>
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
								<label for="shipping_cost" class="d-block"><?= lang('Main.purchases_returns.shipping_cost') ?> (original <?= $settings->currency_symbol ?>0)</label>
								<input type="text" name="shipping_cost" id="shipping_cost" class="form-control" value="0" disabled />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="discount" class="d-block"><?= lang('Main.purchases_returns.discount') ?> (original <?= $settings->currency_symbol ?>0)</label>
								<input type="text" name="discount" id="discount" class="form-control" value="0" disabled />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="tax" class="d-block"><?= lang('Main.purchases_returns.tax') ?> (original 0%)</label>
								<input type="text" name="tax" id="tax" class="form-control" value="0" disabled />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<div class="row mt-n3">
						<div class="col-sm-6 text-break pl-2 pr-2 mt-3">
							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.purchases_returns.notes') ?></label>
								<textarea name="notes" id="notes" class="form-control" rows="6"></textarea>
							</div>
						</div>

						<div class="mt-3 col-sm-2">
							
						</div>

						<div class="col-sm-4 text-break pl-2 pr-2 mt-3">
							<table id="summary" class="table stacked">
								<tbody>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.purchases_returns.subtotal') ?></th>
										<td width="60" data-summary-field="subtotal"><?= $settings->currency_symbol ?> 0</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.purchases_returns.discount') ?></th>
										<td width="60" data-summary-field="discount"><?= $settings->currency_symbol ?> 0</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.purchases_returns.shipping_cost') ?></th>
										<td width="60" data-summary-field="shipping"><?= $settings->currency_symbol ?> 0</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-normal"><?= lang('Main.purchases_returns.tax') ?></th>
										<td width="60" data-summary-field="tax">0%</td>
									</tr>
									<tr>
										<th width="40" class="font-weight-bold"><?= lang('Main.purchases_returns.grand_total') ?></th>
										<td width="60" data-summary-field="total" class="font-weight-bold"><?= $settings->currency_symbol ?> 0</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.purchases_returns.create') ?>
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
let newRefPrep = "<?= addslashes($settings->references_purchase_return_prepend) ?>";
let newRefApp = "<?= addslashes($settings->references_purchase_return_append) ?>";
let newRef = "";
let purchase = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Capture enter key in the reference input
		$('input[name=purchase_reference]').on('keypress', e => {
			if(e.which == 13) {
				e.preventDefault()
				onPurchaseReferenceSubmit()
			}
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
			createReturn()
		})

		<?php if($purchaseId != false) { ?>
		axios.get(`api/purchases/<?= $purchaseId ?>`).then(response => {
			$('input[name=purchase_reference]').val(response.data.reference)
			onPurchaseReferenceSubmit()
		})
		<?php } ?>
	})
})(jQuery)

function onPurchaseReferenceSubmit() {
	let reference = $('input[name=purchase_reference]').val()

	let validator = new Validator()
	validator.addInputText('purchase_reference', 'non-empty', "<?= lang('Validation.purchases.returns_missing_purchase_ref') ?>")
	
	if(!validator.validate())
		return

	axios.get(`api/purchases/reference`, {
		params: {
			reference: reference
		}
	}).then(response => {
		purchase = response.data

		if(purchase.return_id != null) {
			showError("<?= langSlashes('Errors.error') ?>", "<?= lang('Errors.purchases.returns.already_exists') ?>")
			return
		}

		newRef = `${newRefPrep}${reference}${newRefApp}`
		
		$('input[name=reference]').val(newRef)
		$('input[name=date]').val(purchase.created_at)
		$('input[name=warehouse]').val(purchase.warehouse.name)
		$('input[name=supplier]').val(purchase.supplier.name)

		$('table#items tbody').html('')

		purchase.items.forEach((item, i) => {
			purchase.items[i].return_quantity = 0

			let unit_price = Utils.getFloat(item.unit_price)
			let tax = Utils.getFloat(item.tax)
			let quantity = Utils.getFloat(item.quantity)

			let td1 = '<div class="d-flex">'
				+ `<strong>${item.name}</strong>`
				+ '<br />'
				+ '</div>'
			let td2 = unit_price
			let td3 = '<div class="input-group input-group-sm">'
				+ '<input type="text" class="form-control form-control-sm" value="0" />'
				+ '<div class="input-group-append">'
				+ `<span class="input-group-text">/${item.quantity}</span>`
				+ '</div>'
				+ '</div>'
			let td4 = 0
			let td5 = tax
			let td6 = 0

			let elem = `<tr data-item-id="${item.id}">`
				+ `<td>${td1}</td>`
				+ `<td data-item-td="unit_price">${currency} ${td2}</td>`
				+ `<td data-item-td="quantity">${td3}</td>`
				+ `<td data-item-td="subtotal">${currency} ${td4}</td>`
				+ `<td data-item-td="tax">${td5}%</td>`
				+ `<td data-item-td="total">${currency} ${td6}</td>`
				+ '</tr>'

			$('table#items').append(elem)
		})

		$('label[for=shipping_cost]').html(`<?= lang('Main.purchases_returns.shipping_cost') ?> (original ${currency} ${purchase.shipping_cost})`)
		$('label[for=discount]').html(`<?= lang('Main.purchases_returns.discount') ?> (original ${currency} ${purchase.discount})`)
		$('label[for=tax]').html(`<?= lang('Main.purchases_returns.tax') ?> (original ${purchase.tax}%)`)

		$('input[name=shipping_cost]').attr('disabled', false).val(0)
		$('input[name=discount]').attr('disabled', false).val(0)
		$('input[name=tax]').attr('disabled', false).val(0)
		$('textarea[name=notes]').val('')

		$('table#summary tbody tr td[data-summary-field="subtotal"]').html(`${currency} 0`)
		$('table#summary tbody tr td[data-summary-field="discount"]').html(`${currency} 0`)
		$('table#summary tbody tr td[data-summary-field="shipping"]').html(`${currency} 0`)
		$('table#summary tbody tr td[data-summary-field="tax"]').html(`0%`)
		$('table#summary tbody tr td[data-summary-field="total"]').html(`${currency} 0`)

	})
}

function updateTotals() {
	let subtotal = 0

	purchase.items.forEach((item, i) => {
		let field = $(`table#items tbody tr[data-item-id=${item.id}] input`)
		let return_quantity = Utils.getInt(field.val())

		// If quantity is greater than originally purchased, rewrite user input
		if(return_quantity > purchase.items[i].quantity) {
			return_quantity = purchase.items[i].quantity
			$(`table#items tbody tr[data-item-id=${item.id}] input`).val(return_quantity)
		}

		// Update quantity in the original array
		purchase.items[i].return_quantity = return_quantity

		let item_subtotal = Utils.twoDecimals(return_quantity * Utils.getFloat(item.unit_price))
		let item_total = Utils.twoDecimals(Utils.applyTax(Utils.getFloat(item_subtotal), Utils.getFloat(item.tax)))

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

function createReturn() {
	// Perform initial validation
	let validator = new Validator()
	validator.addInputText('purchase_reference', 'non-empty', "<?= langSlashes('Validation.purchases.reference_min_length') ?>")
	validator.addInputText('shipping_cost', 'decimal', "<?= langSlashes('Validation.purchases.shipping_cost_decimal') ?>")
	validator.addInputText('discount', 'decimal', "<?= langSlashes('Validation.purchases.discount_decimal') ?>")
	validator.addInputText('tax', 'decimal', "<?= langSlashes('Validation.purchases.tax_decimal') ?>")

	if(!validator.validate())
		return

	// Make sure we got a purchase
	if(jQuery.isEmptyObject(purchase)) {
		showError("<?= langSlashes('Errors.error') ?>", "<?= langSlashes('Errors.purchases.frontend.return_search_purchase') ?>")
		return
	}

	// Build data object!
	let data = {
		shipping_cost: $('input[name=shipping_cost]').val(),
		discount: $('input[name=discount]').val(),
		tax: $('input[name=tax]').val(),
		notes: $('textarea[name=notes]').val(),
		items: []
	}

	purchase.items.forEach(item => {
		data.items.push({
			id: item.id,
			qty_to_return: item.return_quantity
		})
	})

	axios.post(`api/purchases/${purchase.id}/return`, data).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/purchases/returns/${response.data.id}`
	})
}
</script>
<?= $this->endSection() ?>