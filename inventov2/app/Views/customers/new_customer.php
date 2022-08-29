<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.customers.new_customer') ?>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.customers.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="internal_name" class="d-block"><?= lang('Main.customers.internal_name') ?></label>
								<input type="text" name="internal_name" id="internal_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="company_name" class="d-block"><?= lang('Main.customers.company_name') ?></label>
								<input type="text" name="company_name" id="company_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="tax_number" class="d-block"><?= lang('Main.customers.tax_number') ?></label>
								<input type="text" name="tax_number" id="tax_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="email_address" class="d-block"><?= lang('Main.customers.email_address') ?></label>
								<input type="text" name="email_address" id="email_address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.customers.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<hr class="mt-4 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.physical_information') ?>
							</h6>

							<div class="form-group">
								<label for="address" class="d-block"><?= lang('Main.customers.address') ?></label>
								<input type="text" name="address" id="address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="city" class="d-block"><?= lang('Main.customers.city') ?></label>
										<input type="text" id="city" name="city" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="state" class="d-block"><?= lang('Main.customers.state') ?></label>
										<input type="text" id="state" name="state" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="zip_code" class="d-block"><?= lang('Main.customers.zip_code') ?></label>
										<input type="text" id="zip_code" name="zip_code" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="country" class="d-block"><?= lang('Main.customers.country') ?></label>
										<input type="text" id="country" name="country" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.custom_information') ?>
							</h6>

							<div class="form-group">
								<label for="custom_field_1" class="d-block"><?= lang('Main.customers.custom_field_1') ?></label>
								<input type="text" id="custom_field_1" name="custom_field_1" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="custom_field_2" class="d-block"><?= lang('Main.customers.custom_field_2') ?></label>
								<input type="text" id="custom_field_2" name="custom_field_2" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="custom_field_3" class="d-block"><?= lang('Main.customers.custom_field_3') ?></label>
								<input type="text" id="custom_field_3" name="custom_field_3" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<hr class="mt-4 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.notes') ?>
							</h6>

							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.customers.notes') ?></label>
								<textarea id="notes" name="notes" rows="5" wrap="soft" class="form-control"></textarea>
							</div>

						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.customers.create') ?>
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

			validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.customers.name_min_length') ?>")
			validator.addInputTextVal('name', 'maxLength', 45, "<?= langSlashes('Validation.customers.name_max_length') ?>")
			validator.addInputTextVal('internal_name', 'maxLength', 45, "<?= langSlashes('Validation.customers.internal_name_max_length') ?>")
			validator.addInputTextVal('company_name', 'maxLength', 100, "<?= langSlashes('Validation.customers.company_name_max_length') ?>")
			validator.addInputTextVal('tax_number', 'maxLength', 45, "<?= langSlashes('Validation.customers.tax_number_max_length') ?>")
			validator.addInputText('email_address', 'optional-email-address', "<?= langSlashes('Validation.customers.email_address_invalid') ?>")
			validator.addInputTextVal('phone_number', 'maxLength', 20, "<?= langSlashes('Validation.customers.phone_number_max_length') ?>")
			validator.addInputTextVal('address', 'maxLength', 80, "<?= langSlashes('Validation.customers.address_max_length') ?>")
			validator.addInputTextVal('city', 'maxLength', 80, "<?= langSlashes('Validation.customers.city_max_length') ?>")
			validator.addInputTextVal('country', 'maxLength', 30, "<?= langSlashes('Validation.customers.country_max_length') ?>")
			validator.addInputTextVal('state', 'maxLength', 30, "<?= langSlashes('Validation.customers.state_max_length') ?>")
			validator.addInputText('zip_code', 'optional-integer', "<?= langSlashes('Validation.customers.zip_code_invalid') ?>")
			validator.addInputTextVal('zip_code', 'maxLength', 12, "<?= langSlashes('Validation.customers.zip_code_max_length') ?>")

			if(validator.validate())
				createCustomer()
		})
	})
})(jQuery)

function createCustomer() {
	axios.post('api/customers', {
		name: $('input[name=name]').val(),
		internal_name: $('input[name=internal_name]').val(),
		company_name: $('input[name=company_name]').val(),
		tax_number: $('input[name=tax_number]').val(),
		email_address: $('input[name=email_address]').val(),
		phone_number: $('input[name=phone_number]').val(),
		address: $('input[name=address]').val(),
		city: $('input[name=city]').val(),
		country: $('input[name=country]').val(),
		state: $('input[name=state]').val(),
		zip_code: $('input[name=zip_code]').val(),
		custom_field1: $('input[name=custom_field_1]').val(),
		custom_field2: $('input[name=custom_field_2]').val(),
		custom_field3: $('input[name=custom_field_3]').val(),
		notes: $('textarea[name=notes]').val()
	}).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/customers/${response.data.id}`

	})
}
</script>
<?= $this->endSection() ?>