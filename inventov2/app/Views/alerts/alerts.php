<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('components/error_modal') ?>

<!-- Start of Warehouses -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-3">
			<div class="header">
				<div class="title">
					<?= lang('Main.alerts.alerts') ?>
				</div>

				<div class="desc">
					<?= lang('Main.alerts.alerts_help') ?>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="alerts" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.alerts.item_name') ?></th>
								<th><?= lang('Main.alerts.warehouse_name') ?></th>
								<th><?= lang('Main.alerts.alert_type') ?></th>
								<th><?= lang('Main.alerts.alert_qty_set') ?></th>
								<th><?= lang('Main.alerts.current_qty') ?></th>
								<th><?= lang('Main.alerts.created_at') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Warehouses -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#alerts').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#alerts').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/alerts') ?>",
			columns: [
				{ data: "item_name" },
				{ data: "warehouse_name" },
				{
					data: 'type',
					render: (data, type) => {
						let dataMin = "<?= langSlashes('Main.alerts.min') ?>"
						let dataMax = "<?= langSlashes('Main.alerts.max') ?>"

						if(data == 'min')
							return dataMin
						return dataMax
					}
				},
				{ data: "alert_qty" },
				{ data: "current_qty" },
				{ data: "created_at" }
			],
			order: [[5, 'desc']]
		})

		$('table#alerts tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			location.href = `<?= base_url('items') ?>/${id}`
		})
	})
})(jQuery)
</script>
<?= $this->endSection() ?>