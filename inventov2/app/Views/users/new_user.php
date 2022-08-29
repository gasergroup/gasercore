<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.users.new_user') ?>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.users.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.users.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="email_address" class="d-block"><?= lang('Main.users.email_address') ?>*</label>
								<input type="text" name="email_address" id="email_address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.users.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="role" class="d-block"><?= lang('Main.users.role') ?>*</label>
								<select name="role" id="role" class="custom-select">
									<option value="" disabled selected><?= lang('Main.users.select_role') ?></option>
									<option value="worker"><?= lang('Main.misc.worker') ?></option>
									<option value="supervisor"><?= lang('Main.misc.supervisor') ?></option>
									<option value="admin"><?= lang('Main.misc.admin') ?></option>
								</select>
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.users.account_information') ?>
							</h6>

							<div class="form-group">
								<label for="username" class="d-block"><?= lang('Main.users.username') ?>*</label>
								<input type="text" name="username" id="username" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="password" class="d-block"><?= lang('Main.users.password') ?>*</label>
								<input type="password" name="password" id="password" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="password_confirmation" class="d-block"><?= lang('Main.users.password_confirmation') ?>*</label>
								<input type="password" name="password_confirmation" id="password_confirmation" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.users.create') ?>
						</button>
					</div>
				</form>
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

		$('form').on('submit', e => {
			e.preventDefault()

			let validator = new Validator()
			validator.addInputTextVal('name', 'minLength', 2, "<?= langSlashes('Validation.users.name_min_length') ?>")
			validator.addInputTextVal('name', 'maxLength', 100, "<?= langSlashes('Validation.users.name_max_length') ?>")
			validator.addInputText('email_address', 'email-address', "<?= langSlashes('Validation.users.email_address_invalid') ?>")
			validator.addInputTextVal('phone_number', 'maxLength', 20, "<?= langSlashes('Validation.users.phone_number_max_length') ?>")
			validator.addSelect('role', 'selected', "<?= langSlashes('Validation.users.frontend.role_not_selected') ?>")
			validator.addInputTextVal('username', 'minLength', 5, "<?= langSlashes('Validation.users.username_min_length') ?>")
			validator.addInputTextVal('username', 'maxLength', 30, "<?= langSlashes('Validation.users.username_max_length') ?>")
			validator.addInputTextVal('password', 'minLength', 5, "<?= langSlashes('Validation.users.password_min_length') ?>")
			validator.addInputTextVal('password', 'maxLength', 30, "<?= langSlashes('Validation.users.password_max_length') ?>")
			validator.addInputTextCustom('password_confirmation', value => {
				let password = $('input[name=password]').val()

				return value === password
			}, "<?= langSlashes('Validation.users.password_missmatch') ?>")
			
			if(validator.validate())
				createUser()
		})
	})
})(jQuery)

function createUser() {
	axios.post('api/users', {
		name: $('input[name=name]').val(),
		email_address: $('input[name=email_address').val(),
		phone_number: $('input[name=phone_number]').val(),
		role: $('select[name=role]').val(),
		username: $('input[name=username]').val(),
		password: $('input[name=password]').val()
	}).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/users/${response.data.id}`

	})
}
</script>
<?= $this->endSection() ?>