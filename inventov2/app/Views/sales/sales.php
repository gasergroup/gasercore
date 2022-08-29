<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('sales/modals/sale_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Sales -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.sales.sales') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/sales/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('sales/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.sales.new_sale') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="sales" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.sales.reference') ?></th>
								<th><?= lang('Main.sales.warehouse') ?></th>
								<th><?= lang('Main.misc.created_at') ?></th>
								<th><?= lang('Main.sales.customer.customer') ?></th>
								<th><?= lang('Main.sales.grand_total') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Sales -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';
	
var currency = "<?= $settings->currency_symbol ?>";
var openSale = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#sales').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#sales').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/sales') ?>",
			columns: [
				{ data: "reference" },
				{ data: "warehouse_name" },
				{ data: "created_at" },
				{ data: "customer_name" },
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

		$('table#sales tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadSale(id)
		})

		$('#saleModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/sales`)
		})

		<?php if($saleId != false) { ?>
		loadSale(<?= $saleId ?>)
		<?php } ?>
	})
})(jQuery)

function loadSale(id) {
	axios.get(`api/sales/${id}`).then(response => {
		let sale = response.data

		openSale = sale

		console.log(sale)

		window.history.pushState(null, '', `<?= base_url() ?>/sales/${id}`)

		$('#saleModal').modal('show')
		
		if(sale.return_id == null)
			$('#saleModal button#createReturn').prop('disabled', false)
		else
			$('#saleModal button#createReturn').prop('disabled', true)

		$('#saleModal table#saleInformation td[data-item-field="id"]').text(sale.id)
		$('#saleModal table#saleInformation td[data-item-field="created_at"]').text(sale.created_at)
		$('#saleModal table#saleInformation td[data-item-field="reference"]').text(sale.reference)
		$('#saleModal table#saleInformation td[data-item-field="warehouse_id"]').text(sale.warehouse.id)
		$('#saleModal table#saleInformation td[data-item-field="warehouse_name"]').text(sale.warehouse.name)

		$('#saleModal table#customerInformation td[data-item-field="id"]').text(sale.customer.id)
		$('#saleModal table#customerInformation td[data-item-field="name"]').text(sale.customer.name)
		$('#saleModal table#customerInformation td[data-item-field="address"]').text(sale.customer.address)
		$('#saleModal table#customerInformation td[data-item-field="city"]').text(sale.customer.city)
		$('#saleModal table#customerInformation td[data-item-field="state"]').text(sale.customer.state)
		$('#saleModal table#customerInformation td[data-item-field="zip_code"]').text(sale.customer.zip_code)
		$('#saleModal table#customerInformation td[data-item-field="country"]').text(sale.customer.country)

		$('#saleModal table#items tbody').html('')

		sale.items.forEach(item => {
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

			$('#saleModal table#items tbody').append(elem)
		})
		
		$('#saleModal #notes').html(sale.notes)

		$('#saleModal table#summary td[data-summary-field="subtotal"]').html(`${currency} ${Utils.twoDecimals(sale.subtotal)}`)
		$('#saleModal table#summary td[data-summary-field="discount"]').html(`${currency} ${Utils.twoDecimals(sale.discount)}`)
		$('#saleModal table#summary td[data-summary-field="shipping"]').html(`${currency} ${Utils.twoDecimals(sale.shipping_cost)}`)
		$('#saleModal table#summary td[data-summary-field="tax"]').html(`${Utils.twoDecimals(sale.tax)}%`)
		$('#saleModal table#summary td[data-summary-field="total"]').html(`${currency} ${Utils.twoDecimals(sale.grand_total)}`)
	})
}

function createReturn() {
	location.href = `<?= base_url() ?>/sales/${openSale.id}/return`
}
</script>
<?= $this->endSection() ?>