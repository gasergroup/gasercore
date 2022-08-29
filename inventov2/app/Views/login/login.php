<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<title><?= $settings->site_title; ?></title>

		<link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
		<link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
		
		<script type="text/javascript" src="<?= base_url('assets/js/jquery-3.6.0.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/popper-2.9.2.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/bootstrap-4.6.0-dist/js/bootstrap.min.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/inventov2_validator.js') ?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/js/axios-0.21.1.min.js') ?>"></script>

		<link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>" />
		<link rel="stylesheet" href="<?= base_url('assets/bootstrap-4.6.0-dist/css/bootstrap.min.css') ?>" />
	</head>

	<body>
		<div id="modal" class="modal fade">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h6 class="modal-title"></h6>
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
							<?= lang('Main.misc.ok') ?>
						</button>
					</div>
				</div>
			</div>
		</div>

		<div id="app" class="full-height">
			<div class="main-loader">
				<div class="loader">	
					<div class="bounce1"></div>
					<div class="bounce2"></div>
					<div class="bounce3"></div>
				</div>
			</div>

			<div class="container">
				<div class="row text-center">
					<div class="align-self-center mx-auto col-12">
						<div id="login-box" class="text-left mx-auto">
							<div class="logo text-center">
								<a href="<?= base_url('login'); ?>">
									<img src="<?= base_url('assets/images/logo/logo.png') ?>">
								</a>
							</div>

							<form class="mb-0" novalidate>
								<div class="form-group">
									<label for="username" class="col-form-label pt-0"><?= lang('Main.login.username') ?></label>
									<input id="username" name="username" type="text" class="form-control" />
									<div class="invalid-feedback"></div>
								</div>

								<div class="form-group">
									<label for="password" class="col-form-label pt-0"><?= lang('Main.login.password') ?></label>
									<input id="password" name="password" type="password" class="form-control" />
									<div class="invalid-feedback"></div>
								</div>

								<button type="submit" class="btn float-right mt-3 btn-primary btn-sm">
									<?= lang('Main.login.login') ?>
								</button>

								<div class="clearfix"></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">		
		(function($) {
			'use strict';
				
			$('document').ready(function() {
				$('.loader').fadeOut();
				let validator = new Validator();

				$('form').on('submit', e => {
					e.preventDefault();

					validator.addInputText('input#username', 'non-empty', "Please type your username")
					validator.addInputText('input#password', 'non-empty', "Please type your password")

					if(validator.validate()) {
						$('.loader').fadeIn();

						axios.post("<?= base_url('api/login'); ?>", {
							username: $('input#username').val(),
							password: $('input#password').val(),
							type: 'session'
						}).then(function(response) {
							location.href = "<?= base_url(); ?>"

						}).catch(function(error) {
							$('.modal').find('.modal-title').text('Error')

							if(error.response.data && error.response.data.messages && error.response.data.messages.error)
								$('.modal').find('.modal-body p').text(error.response.data.messages.error)
							
							$('.modal').modal('show')
						}).then(() => $('.loader').fadeOut())
					}
				});
			});
		})(jQuery)
		</script>
	</body>
</html>