<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<title><?= $settings->site_title ?></title>

		<link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
		<link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

		<script type="text/javascript" src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/bootstrap-4.6.0-dist/js/bootstrap.bundle.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/inventov2_master.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/inventov2_validator.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/axios-0.21.1.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/chart-3.4.1.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/jsbarcode-3.11.4.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/qrcode.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/datatables/datatables.min.js') ?>"></script>

		<link rel="stylesheet" href="<?= base_url('assets/bootstrap-4.6.0-dist/css/bootstrap.min.css') ?>" />
		<link rel="stylesheet" href="<?= base_url('assets/fontawesome-5.15.3/css/all.css') ?>" />
		<link rel="stylesheet" href="<?= base_url('assets/datatables/datatables.min.css') ?>" />
		<link rel="stylesheet" href="<?= base_url('assets/css/master.css') ?>" />

		<script type="text/javascript">
		axios.defaults.baseURL = '<?= base_url() ?>/'
		let _errorTitle = "<?= langSlashes('Errors.error') ?>"
		let _errorContent = "<?= langSlashes('Errors.unexpected_error') ?>"
		$.extend($.fn.dataTable.defaults, {
			language: {
				info: "<?= langSlashes('Main.misc.tables.info') ?>",
				infoEmpty: "<?= langSlashes('Main.misc.tables.infoEmpty') ?>",
				infoFiltered: "<?= langSlashes('Main.misc.tables.infoFiltered') ?>",
				lengthMenu: "<?= langSlashes('Main.misc.tables.lengthMenu') ?>",
				search: "<?= langSlashes('Main.misc.tables.search') ?>",
				zeroRecords: "<?= langSlashes('Main.misc.tables.zeroRecords') ?>",
				paginate: {
					next: "<?= langSlashes('Main.misc.tables.paginate.next') ?>",
					previous: "<?= langSlashes('Main.misc.tables.paginate.previous') ?>",
				}
			}
		})
		</script>
	</head>

	<body>
		<?= $this->include('components/loader'); ?>

		<?= $this->include('components/header'); ?>

		<div id="main" class="d-flex">
			<?= $this->include('components/sidebar'); ?>

			<div class="container-fluid pt-3 mb-4">
				<?= $this->renderSection('content'); ?>

				<div id="version">
					<?= lang('Main.dashboard.version') ?> <?= $settings->version ?>
				</div>
			</div>
		</div>

		<?= $this->renderSection('js'); ?>
	</body>
</html>