<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('transfers/modals/transfer_modal') ?>
<?= $this->include('components/error_modal') ?>

<!-- Start of Transfers -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.transfers.transfers') ?>
				</div>

				<div class="buttons d-flex">
					<a href="<?= base_url('transfers/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.transfers.new_transfer') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="transfers" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.transfers.from_warehouse_name') ?></th>
								<th><?= lang('Main.transfers.to_warehouse_name') ?></th>
								<th><?= lang('Main.transfers.created_by') ?></th>
								<th><?= lang('Main.transfers.created_at') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Transfers -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';
	
var openTransfer = {};
var table = {};

let arrowRight = `<i class="fas fa-long-arrow-alt-right"></i>`;

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#transfers').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#transfers').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/transfers') ?>",
			columns: [
				{ data: "from_warehouse_name" },
				{ data: "to_warehouse_name" },
				{ data: "created_by" },
				{ data: "created_at" }
			],
			order: [[2, 'desc']]
		})

		$('table#transfers tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadTransfer(id)
		})

		$('#transferModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/transfers`)
		})

		<?php if($transferId != false) { ?>
		loadTransfer(<?= $transferId ?>)
		<?php } ?>
	})
})(jQuery)

function loadTransfer(id) {
	axios.get(`api/transfers/${id}`).then(response => {
		let transfer = response.data

		openTransfer = transfer

		window.history.pushState(null, '', `<?= base_url() ?>/transfers/${id}`)

		$('#transferModal').modal('show')

		$('#transferModal table#transferInformation[data-item-field="id"]').text(transfer.id)
		$('#transferModal table#transferInformation td[data-item-field="from_warehouse_id"]').text(transfer.from_warehouse.id)
		$('#transferModal table#transferInformation td[data-item-field="from_warehouse_name"]').text(transfer.from_warehouse.name)
		$('#transferModal table#transferInformation td[data-item-field="to_warehouse_id"]').text(transfer.to_warehouse.id)
		$('#transferModal table#transferInformation td[data-item-field="to_warehouse_name"]').text(transfer.to_warehouse.name)
		$('#transferModal table#transferInformation td[data-item-field="created_by"]').text(transfer.created_by.name)
		$('#transferModal table#transferInformation td[data-item-field="created_at"]').text(transfer.created_at)

		$('#transferModal table#items tbody').html('')

		transfer.items.forEach(item => {
			let originalFromQuantity = Utils.getInt(item.original_from_quantity)
			let originalToQuantity = Utils.getInt(item.original_to_quantity)
			let transferQuantity = Utils.getInt(item.transfer_quantity)
			let stockAfterTransferFrom = originalFromQuantity - transferQuantity
			let stockAfterTransferTo = originalToQuantity + transferQuantity

			/*
			<th><?= lang('Main.transfers.items.item_name') ?></th>
			<th><?= lang('Main.transfers.items.transfer_quantity') ?></th>
			<th><?= lang('Main.transfers.items.source_warehouse_change') ?></th>
			<th><?= lang('Main.transfers.items.target_warehouse_change') ?></th>
			 */

			/*
			let td4 = `${item.quantities.from.quantity} ${arrowRight} ${item.quantities.from.quantity}`
			let td5 = `${item.quantities.to.quantity} ${arrowRight} ${item.quantities.to.quantity}`
			 */

			let elem = '<tr>'
				+ '<td>'
				+ `<strong>${item.name}</strong><br />${item.code}`
				+ '</td>'
				+ `<td>${transferQuantity}</td>`
				+ `<td>${originalFromQuantity} ${arrowRight} ${stockAfterTransferFrom}</td>`
				+ `<td>${originalToQuantity} ${arrowRight} ${stockAfterTransferTo}</td>`

			$('#transferModal table#items tbody').append(elem)
		})

		$('#transferModal #notes').html(transfer.notes)
	})
}
</script>
<?= $this->endSection() ?>