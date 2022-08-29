<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('sales/modals/sale_return_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Sales returns -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.sales_returns.sales_returns') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/sales/returns/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('sales/returns/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.sales_returns.new_return') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="returns" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.sales_returns.reference') ?></th>
								<th><?= lang('Main.sales_returns.sale_reference') ?></th>
								<th><?= lang('Main.sales_returns.warehouse') ?></th>
								<th><?= lang('Main.misc.created_at') ?></th>
								<th><?= lang('Main.sales_returns.customer.customer') ?></th>
								<th><?= lang('Main.sales_returns.total_refunded') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Sales returns -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';
	
var currency = "<?= $settings->currency_symbol ?>";
var openReturn = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#returns').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#returns').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/sales/returns') ?>",
			columns: [
				{ data: "reference" },
				{ data: "sale_reference" },
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

		$('table#returns tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadReturn(id)
		})

		$('#returnModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/sales/returns`)
		})

		<?php if($returnId != false) { ?>
			loadReturn(<?= $returnId ?>)
		<?php } ?>
	})
})(jQuery)

function loadReturn(id) {
	axios.get(`api/sales/returns/${id}`).then(response => {
		let saleReturn = response.data

		openReturn = saleReturn

		window.history.pushState(null, '', `<?= base_url() ?>/sales/returns/${id}`)

		$('#returnModal').modal('show')

		$('#returnModal table#returnInformation td[data-item-field="created_at"]').text(saleReturn.created_at)
		$('#returnModal table#returnInformation td[data-item-field="reference"]').text(saleReturn.reference)
		$('#returnModal table#returnInformation td[data-item-field="sale_reference"]').text(saleReturn.sale.reference)
		$('#returnModal table#returnInformation td[data-item-field="warehouse"]').text(saleReturn.warehouse.name)

		$('#returnModal table#customerInformation td[data-item-field="id"]').text(saleReturn.customer.id)
		$('#returnModal table#customerInformation td[data-item-field="name"]').text(saleReturn.customer.name)
		$('#returnModal table#customerInformation td[data-item-field="address"]').text(saleReturn.customer.address)
		$('#returnModal table#customerInformation td[data-item-field="city"]').text(saleReturn.customer.city)
		$('#returnModal table#customerInformation td[data-item-field="state"]').text(saleReturn.customer.state)
		$('#returnModal table#customerInformation td[data-item-field="zip_code"]').text(saleReturn.customer.zip_code)
		$('#returnModal table#customerInformation td[data-item-field="country"]').text(saleReturn.customer.country)

		$('#returnModal table#items tbody').html('')

		saleReturn.items.forEach(item => {
			let unit_price = Utils.getFloat(item.unit_price)
			let quantity = Utils.getInt(item.qty_to_return)
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

			$('#returnModal table#items tbody').append(elem)
		})

		$('#returnModal #notes').html(saleReturn.notes)

		$('#returnModal table#summary td[data-summary-field="subtotal"]').html(`${currency} ${Utils.twoDecimals(saleReturn.subtotal)}`)
		$('#returnModal table#summary td[data-summary-field="discount"]').html(`${currency} ${Utils.twoDecimals(saleReturn.discount)}`)
		$('#returnModal table#summary td[data-summary-field="shipping"]').html(`${currency} ${Utils.twoDecimals(saleReturn.shipping_cost)}`)
		$('#returnModal table#summary td[data-summary-field="tax"]').html(`${Utils.twoDecimals(saleReturn.tax)}%`)
		$('#returnModal table#summary td[data-summary-field="total"]').html(`${currency} ${Utils.twoDecimals(saleReturn.grand_total)}`)
	})
}
</script>
<?= $this->endSection() ?>