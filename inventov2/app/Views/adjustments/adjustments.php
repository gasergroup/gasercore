<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('adjustments/modals/adjustment_modal') ?>
<?= $this->include('components/error_modal') ?>

<!-- Start of Adjustments -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.adjustments.adjustments') ?>
				</div>

				<div class="buttons d-flex">
					<a href="<?= base_url('adjustments/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.adjustments.new_adjustment') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="adjustments" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.adjustments.warehouse_name') ?></th>
								<th><?= lang('Main.adjustments.created_by') ?></th>
								<th><?= lang('Main.adjustments.created_at') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Adjustments -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';
	
var openAdjustment = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#adjustments').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#adjustments').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/adjustments') ?>",
			columns: [
				{ data: "warehouse_name" },
				{ data: "created_by" },
				{ data: "created_at" }
			],
			order: [[2, 'desc']]
		})

		$('table#adjustments tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadAdjustment(id)
		})

		$('#adjustmentModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/adjustments`)
		})

		<?php if($adjustmentId != false) { ?>
		loadAdjustment(<?= $adjustmentId ?>)
		<?php } ?>
	})
})(jQuery)

function loadAdjustment(id) {
	axios.get(`api/adjustments/${id}`).then(response => {
		let adjustment = response.data

		openAdjustment = adjustment

		window.history.pushState(null, '', `<?= base_url() ?>/adjustments/${id}`)

		$('#adjustmentModal').modal('show')

		$('#adjustmentModal table#adjustmentInformation td[data-item-field="id"]').text(adjustment.id)
		$('#adjustmentModal table#adjustmentInformation td[data-item-field="warehouse_id"]').text(adjustment.warehouse.id)
		$('#adjustmentModal table#adjustmentInformation td[data-item-field="warehouse_name"]').text(adjustment.warehouse.name)
		$('#adjustmentModal table#adjustmentInformation td[data-item-field="created_by"]').text(adjustment.created_by.name)
		$('#adjustmentModal table#adjustmentInformation td[data-item-field="created_at"]').text(adjustment.created_at)

		$('#adjustmentModal table#items tbody').html('')

		adjustment.items.forEach(item => {
			let originalQuantity = Utils.getInt(item.quantity)
			let adjustmentQuantity = Utils.getInt(item.adjustment_quantity)

			let quantityAfterAdjustment = originalQuantity
			if(item.adjustment_type == 'add')
				quantityAfterAdjustment += adjustmentQuantity
			else
				quantityAfterAdjustment -= adjustmentQuantity

			let adjustmentType = ""
			if(item.adjustment_type == 'add')
				adjustmentType = "<?= langSlashes('Main.adjustments.add') ?>"
			else
				adjustmentType = "<?= langSlashes('Main.adjustments.subtract') ?>"

			let elem = '<tr>'
				+ '<td>'
				+ `<strong>${item.name}</strong><br />${item.code}`
				+ '</td>'
				+ `<td>${item.quantity}</td>`
				+ `<td>${adjustmentType}</td>`
				+ `<td>${item.adjustment_quantity}</td>`
				+ `<td>${quantityAfterAdjustment}</td>`

			$('#adjustmentModal table#items tbody').append(elem)
		})

		$('#adjustmentModal #notes').html(adjustment.notes)
	})
}
</script>
<?= $this->endSection() ?>