<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('purchases/modals/purchase_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Purchases -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.purchases.purchases') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/purchases/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('purchases/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.purchases.new_purchase') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="purchases" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.purchases.reference') ?></th>
								<th><?= lang('Main.purchases.warehouse') ?></th>
								<th><?= lang('Main.misc.created_at') ?></th>
								<th><?= lang('Main.purchases.supplier.supplier') ?></th>
								<th><?= lang('Main.purchases.grand_total') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Purchases -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

var currency = "<?= addslashes($settings->currency_symbol) ?>";
var openPurchase = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#purchases').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#purchases').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/purchases') ?>",
			columns: [
				{ data: "reference" },
				{ data: "warehouse_name" },
				{ data: "created_at" },
				{ data: "supplier_name" },
				{
					data: "grand_total",
					render: (data, type) => {
						let finalData = data

						if(type == 'display')
							finalData = `${currency} ${data}`

						return finalData
					}
				}
			]
		})

		$('table#purchases tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadPurchase(id)
		})

		$('#purchaseModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/users`)
		})

		<?php if($purchaseId != false) { ?>
		loadPurchase(<?= $purchaseId ?>)
		<?php } ?>
	})
})(jQuery)

function loadPurchase(id) {
	axios.get(`api/purchases/${id}`).then(response => {
		let purchase = response.data

		openPurchase = purchase

		window.history.pushState(null, '', `<?= base_url() ?>/purchases/${id}`)

		$('#purchaseModal').modal('show')

		if(purchase.return_id == null)
			$('#purchaseModal button#createReturn').prop('disabled', false)
		else
			$('#purchaseModal button#createReturn').prop('disabled', true)

		$('#purchaseModal table#purchaseInformation td[data-item-field="id"]').text(purchase.id)
		$('#purchaseModal table#purchaseInformation td[data-item-field="created_at"]').text(purchase.created_at)
		$('#purchaseModal table#purchaseInformation td[data-item-field="reference"]').text(purchase.reference)
		$('#purchaseModal table#purchaseInformation td[data-item-field="warehouse_id"]').text(purchase.warehouse.id)
		$('#purchaseModal table#purchaseInformation td[data-item-field="warehouse_name"]').text(purchase.warehouse.name)

		$('#purchaseModal table#supplierInformation td[data-item-field="id"]').text(purchase.supplier.id)
		$('#purchaseModal table#supplierInformation td[data-item-field="name"]').text(purchase.supplier.name)
		$('#purchaseModal table#supplierInformation td[data-item-field="address"]').text(purchase.supplier.address)
		$('#purchaseModal table#supplierInformation td[data-item-field="city"]').text(purchase.supplier.city)
		$('#purchaseModal table#supplierInformation td[data-item-field="state"]').text(purchase.supplier.state)
		$('#purchaseModal table#supplierInformation td[data-item-field="zip_code"]').text(purchase.supplier.zip_code)
		$('#purchaseModal table#supplierInformation td[data-item-field="country"]').text(purchase.supplier.country)

		$('#purchaseModal table#items tbody').html('')

		purchase.items.forEach(item => {
			let unit_price = Utils.getFloat(item.unit_price)
			let quantity = Utils.getInt(item.quantity)
			let tax = Utils.getFloat(item.tax)

			let subtotal = quantity * unit_price
			let total = Utils.applyTax(subtotal, tax)

			let elem = '<tr>'
				+ `<td>`
				+ `<strong>${item.name}</strong><br />${item.code}`
				+ `</td>`
				+ `<td>${currency} ${Utils.twoDecimals(unit_price)}</td>`
				+ `<td>${quantity}</td>`
				+ `<td>${currency} ${Utils.twoDecimals(subtotal)}</td>`
				+ `<td>${Utils.twoDecimals(tax)}%</td>`
				+ `<td>${currency} ${Utils.twoDecimals(total)}</td>`
				+ '</tr>'

			$('#purchaseModal table#items tbody').append(elem)
		})

		$('#purchaseModal #notes').html(purchase.notes)

		$('#purchaseModal table#summary td[data-summary-field="subtotal"]').html(`${currency} ${Utils.twoDecimals(purchase.subtotal)}`)
		$('#purchaseModal table#summary td[data-summary-field="discount"]').html(`${currency} ${Utils.twoDecimals(purchase.discount)}`)
		$('#purchaseModal table#summary td[data-summary-field="shipping"]').html(`${currency} ${Utils.twoDecimals(purchase.shipping_cost)}`)
		$('#purchaseModal table#summary td[data-summary-field="tax"]').html(`${Utils.twoDecimals(purchase.tax)}%`)
		$('#purchaseModal table#summary td[data-summary-field="total"]').html(`${currency} ${Utils.twoDecimals(purchase.grand_total)}`)
	})
}

function createReturn() {
	location.href = `<?= base_url() ?>/purchases/${openPurchase.id}/return`
}
</script>
<?= $this->endSection() ?>