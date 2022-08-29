<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('components/error_modal') ?>

<div class="modal fade" id="scanItemBarcodeModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.dashboard.worker.scan_item') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<div class="row mt-0">
						<div class="col-sm text-break pl-2 pr-2">
							<?= lang('Main.dashboard.worker.scan_item_help') ?>
							<input id="barcode" name="barcode" type="text" class="form-control form-control-lg mt-2" />
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm"><?= lang('Main.dashboard.worker.search') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="px-2 py-2 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.dashboard.worker.quick_actions') ?>
			</div>

			<div class="content">
				<a href="#" onclick="onScanItemBarcode(event)" class="btn btn-outline-primary btn-worker-dashboard">
					<i class="fas fa-barcode"></i>
						<?= lang('Main.dashboard.worker.scan_item') ?>
				</a>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="px-2 py-2 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.dashboard.worker.sales_and_purchases') ?>
			</div>

			<div class="content">
				<a href="<?= base_url('sales/new') ?>" class="btn btn-outline-success btn-worker-dashboard">
					<i class="fas fa-money-bill-alt"></i>
					<?= lang('Main.dashboard.worker.new_sale') ?>
				</a>

				<a href="<?= base_url('purchases/new') ?>" class="btn btn-outline-success btn-worker-dashboard">
					<i class="fas fa-handshake"></i>
					<?= lang('Main.dashboard.worker.new_purchase') ?>
				</a>

				<a href="<?= base_url('sales/returns/new') ?>" class="btn btn-outline-danger btn-worker-dashboard">
					<i class="fas fa-exchange-alt"></i>
					<?= lang('Main.dashboard.worker.new_sale_return') ?>
				</a>

				<a href="<?= base_url('purchases/returns/new') ?>" class="btn btn-outline-danger btn-worker-dashboard">
					<i class="fas fa-exchange-alt"></i>
					<?= lang('Main.dashboard.worker.new_purchase_return') ?>
				</a>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="px-2 py-2 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.dashboard.worker.others') ?>
			</div>

			<div class="content">
				<a href="<?= base_url('items/new') ?>" class="btn btn-outline-primary btn-worker-dashboard">
					<i class="fas fa-plus"></i>
					<?= lang('Main.dashboard.worker.new_item') ?>
				</a>
				
				<a href="<?= base_url('customers/new') ?>" class="btn btn-outline-primary btn-worker-dashboard">
					<i class="fas fa-user-tag"></i>
					<?= lang('Main.dashboard.worker.new_customer') ?>
				</a>

				<a href="<?= base_url('suppliers/new') ?>" class="btn btn-outline-primary btn-worker-dashboard">
					<i class="fas fa-industry"></i>
					<?= lang('Main.dashboard.worker.new_supplier') ?>
				</a>

				<a href="<?= base_url('brands/new') ?>" class="btn btn-outline-primary btn-worker-dashboard">
					<i class="fas fa-certificate"></i>
					<?= lang('Main.dashboard.worker.new_brand') ?>
				</a>

				<a href="<?= base_url('categories/new') ?>" class="btn btn-outline-primary btn-worker-dashboard">
					<i class="fas fa-tag"></i>
					<?= lang('Main.dashboard.worker.new_category') ?>
				</a>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

(function($) {
	'use strict';
	
	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// When user searches for item code
		$('form').on('submit', e => {
			e.preventDefault()

			let itemCode = $('input[name=barcode]').val()

			if(itemCode == '') {
				showError('<?= langSlashes('Errors.error') ?>', '<?= langSlashes('Main.dashboard.worker.no_item_code') ?>')
				return
			}

			axios.get(`api/items/code`, {
				params: {
					code: itemCode
				}
			}).then(response => {
				let itemId = response.data.id

				location.href = `<?= base_url('items') ?>/${itemId}`
				
			})
		})
	})
})(jQuery)

function onScanItemBarcode(e) {
	e.preventDefault()
	$('#scanItemBarcodeModal').modal('show')
}
</script>
<?= $this->endSection() ?>