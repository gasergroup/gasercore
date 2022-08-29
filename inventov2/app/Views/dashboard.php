<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<!-- Timeframe selector -->
<div class="timeframe d-flex justify-content-end" id="initStatsTimeframes">
	<ul>
		<li class="active" data-timeframe="today">
			<a href="#" onclick="loadInitStats('today')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.today')) ?></a>
		</li>
		<li data-timeframe="7-days">
			<a href="#" onclick="loadInitStats('7-days')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.last_7_days')) ?></a>
		</li>
		<li data-timeframe="this-month">
			<a href="#" onclick="loadInitStats('this-month')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.this_month')) ?></a>
		</li>
		<li data-timeframe="this-year">
			<a href="#" onclick="loadInitStats('this-year')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.this_year')) ?></a>
		</li>
		<li data-timeframe="last-year">
			<a href="#" onclick="loadInitStats('last-year')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.last_year')) ?></a>
		</li>
	</ul>
</div>

<!-- Stat cards -->
<div class="row mt-2" id="initStats">
	<div class="px-2 py-1 col-sm-4 col-md-3 col-6">
		<div class="section variant-1">
			<div class="header">
				<?= mb_strtoupper(lang('Main.dashboard.revenue')) ?>
			</div>
			<div class="value">
				<?= $settings->currency_symbol ?> 0.00
			</div>
		</div>
	</div>

	<div class="px-2 py-1 col-sm-4 col-md-3 col-6">
		<div class="section variant-1">
			<div class="header">
				<?= mb_strtoupper(lang('Main.dashboard.profits')) ?>
			</div>
			<div class="value">
				<?= $settings->currency_symbol ?> 0.00
			</div>
		</div>
	</div>

	<div class="px-2 py-1 col-sm-4 col-md-3 col-6">
		<div class="section variant-1">
			<div class="header red">
				<?= mb_strtoupper(lang('Main.dashboard.purchases')) ?>
			</div>
			<div class="value">
				<?= $settings->currency_symbol ?> 0.00
			</div>
		</div>
	</div>

	<div class="px-2 py-1 col-sm-4 col-md-3 col-6">
		<div class="section variant-1">
			<div class="header">
				<?= mb_strtoupper(lang('Main.dashboard.value_in_stock')) ?>
			</div>
			<div class="value">
				<?= $settings->currency_symbol ?> 0.00
			</div>
		</div>
	</div>
</div>

<!-- Incomes and expenses -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.dashboard.incomes_expenses') ?>
				</div>

				<div class="timeframe d-flex" id="incomesExpensesTimeframes">
					<ul>
						<li class="active" data-timeframe="7-days">
							<a href="#" onclick="loadIncomesExpenses('7-days')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.7_days')) ?></a>
						</li>
						<li data-timeframe="this-month">
							<a href="#" onclick="loadIncomesExpenses('this-month')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.this_month')) ?></a>
						</li>
						<li data-timeframe="last-month">
							<a href="#" onclick="loadIncomesExpenses('last-month')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.last_month')) ?></a>
						</li>
						<li data-timeframe="this-year">
							<a href="#" onclick="loadIncomesExpenses('this-year')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.this_year')) ?></a>
						</li>
						<li data-timeframe="last-year">
							<a href="#" onclick="loadIncomesExpenses('last-year')"><?= mb_strtoupper(lang('Main.dashboard.timeframes.last_year')) ?></a>
						</li>
					</ul>
				</div>
			</div>

			<div class="content">
				<canvas id="incomesExpenses" height="350"></canvas>
			</div>
		</div>
	</div>
</div>

<!-- Latest sales -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.dashboard.latest_sales') ?>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="salesTable" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.misc.created_at') ?></th>
								<th><?= lang('Main.dashboard.reference') ?></th>
								<th><?= lang('Main.dashboard.customer') ?></th>
								<th><?= lang('Main.dashboard.grand_total') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Latest purchases -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.dashboard.latest_purchases') ?>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="purchasesTable" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.misc.created_at') ?></th>
								<th><?= lang('Main.dashboard.reference') ?></th>
								<th><?= lang('Main.dashboard.supplier') ?></th>
								<th><?= lang('Main.dashboard.grand_total') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

let incomesExpenses, salesTable, purchasesTable;
let currency = "<?= $settings->currency_symbol ?>";

(function($) {
	'use strict';

	$('document').ready(function() {

		incomesExpenses = new Chart($('canvas#incomesExpenses'), {
			type: 'line',
			data: {
				labels: ['a', 'b'],
				datasets: [
					{
						label: '<?= langSlashes('Main.dashboard.incomes') ?>',
						backgroundColor: 'rgba(71, 188, 71, 0.2)',
						borderColor: 'rgba(123, 191, 117, 1)',
						data: []
					}, {
						label: '<?= langSlashes('Main.dashboard.expenses') ?>',
						backgroundColor: 'rgba(235, 76, 76, 0.2)',
						borderColor: 'rgba(223, 134, 134, 1)',
						data: []
					}, {
						label: '<?= langSlashes('Main.dashboard.profits') ?>',
						backgroundColor: 'rgba(71, 148, 188, 0.2)',
						borderColor: 'rgba(117, 144, 191, 1)',
						data: [],
						hidden: true
					}
				]
			},
			options: {
				scales: {
					y: {
						beginAtZero: true,
						precision: 0,
						title: {
							display: true,
							text: "<?= strtoupper(langSlashes('Main.dashboard.amount')) ?>"
						}
					},

					x: {
						title: {
							display: true,
							text: "<?= strtoupper(langSlashes('Main.dashboard.days')) ?>"
						}
					}
				},

				maintainAspectRatio: false,

				elements: {
					line: {
						borderWidth: 2,
						tension: 0
					}
				},

				legend: {
					labels: {
						usePointStyle: true
					}
				}
			}
		})

		// When clicking sale/purchase from table
		$('table#salesTable tbody').on('click', 'tr', function() {
			let id = salesTable.row(this).data().DT_RowId
			location.href = `<?= base_url('sales') ?>/${id}`
		})

		$('table#purchasesTable tbody').on('click', 'tr', function() {
			let id = purchasesTable.row(this).data().DT_RowId
			location.href = `<?= base_url('purchases') ?>/${id}`
		})

		loadInitStats('today')
		loadIncomesExpenses('7-days')
		loadTables()
	})
})(jQuery)

function loadInitStats(timeframe) {
	return axios.get('api/stats/' + timeframe).then(response => {
		$('#initStatsTimeframes ul li.active').removeClass('active')
		$('#initStatsTimeframes ul li[data-timeframe=' + timeframe + ']').addClass('active')

		$('#initStats > div:nth-child(1) > .section > .value').text(`${currency} ${response.data.revenue}`)
		$('#initStats > div:nth-child(2) > .section > .value').text(`${currency} ${response.data.profits}`)
		$('#initStats > div:nth-child(3) > .section > .value').text(`${currency} ${response.data.purchases}`)
		$('#initStats > div:nth-child(4) > .section > .value').text(`${currency} ${response.data.value_in_stock}`)
	})
}

function loadIncomesExpenses(timeframe) {
	return axios.get('api/stats/cash-flow/' + timeframe).then(response => {
		$('#incomesExpensesTimeframes ul li.active').removeClass('active')
		$('#incomesExpensesTimeframes ul li[data-timeframe=' + timeframe + ']').addClass('active')

		incomesExpenses.data.labels = response.data.labels

		incomesExpenses.data.datasets[0].data = response.data.incomes
		incomesExpenses.data.datasets[1].data = response.data.expenses
		incomesExpenses.data.datasets[2].data = response.data.profits

		incomesExpenses.update()
	})
}

function loadTables() {
	salesTable = $('#salesTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: "<?= base_url('api/sales/latest-table') ?>",
		columns: [
			{ data: "created_at" },
			{ data: "reference" },
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
		],
		paging: false,
		searching: false,
		ordering: false,
		info: false
	})

	purchasesTable = $('#purchasesTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: "<?= base_url('api/purchases/latest-table') ?>",
		columns: [
			{ data: "created_at" },
			{ data: "reference" },
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
		],
		paging: false,
		searching: false,
		ordering: false,
		info: false
	})
}
</script>
<?= $this->endSection() ?>