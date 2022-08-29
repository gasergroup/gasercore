<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.warehouses.new_warehouse') ?>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.warehouses.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.warehouses.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.warehouses.physical_information') ?>
							</h6>

							<div class="form-group">
								<label for="address" class="d-block"><?= lang('Main.warehouses.address') ?></label>
								<input type="text" name="address" id="address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="city" class="d-block"><?= lang('Main.warehouses.city') ?></label>
										<input type="text" id="city" name="city" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="state" class="d-block"><?= lang('Main.warehouses.state') ?></label>
										<input type="text" id="state" name="state" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="zip_code" class="d-block"><?= lang('Main.warehouses.zip_code') ?></label>
										<input type="text" id="zip_code" name="zip_code" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="country" class="d-block"><?= lang('Main.warehouses.country') ?></label>
										<input type="text" id="country" name="country" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.warehouses.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.warehouses.create') ?>
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
			validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.warehouses.name_min_length') ?>")
			validator.addInputTextVal('name', 'maxLength', 100, "<?= langSlashes('Validation.warehouses.name_max_length') ?>")
			validator.addInputTextVal('address', 'maxLength', 80, "<?= langSlashes('Validation.warehouses.address_max_length') ?>")
			validator.addInputTextVal('city', 'maxLength', 80, "<?= langSlashes('Validation.warehouses.city_max_length') ?>")
			validator.addInputTextVal('country', 'maxLength', 30, "<?= langSlashes('Validation.warehouses.country_max_length') ?>")
			validator.addInputTextVal('state', 'maxLength', 30, "<?= langSlashes('Validation.warehouses.state_max_length') ?>")
			validator.addInputText('zip_code', 'optional-integer', "<?= langSlashes('Validation.warehouses.zip_code_invalid') ?>")
			validator.addInputTextVal('zip_code', 'maxLength', 12, "<?= langSlashes('Validation.warehouses.zip_code_max_length') ?>")
			validator.addInputTextVal('zip_code', 'maxLength', 12, "<?= langSlashes('Validation.warehouses.zip_code_max_length') ?>")
			validator.addInputTextVal('phone_number', 'maxLength', 20, "<?= langSlashes('Validation.warehouses.phone_number_max_length') ?>")

			if(validator.validate())
				createWarehouse()
		})
	})
})(jQuery)

function createWarehouse() {
	axios.post('api/warehouses', {
		name: $('input[name=name]').val(),
		address: $('input[name=address]').val(),
		city: $('input[name=city]').val(),
		state: $('input[name=state]').val(),
		zip_code: $('input[name=zip_code]').val(),
		country: $('input[name=country]').val(),
		phone_number: $('input[name=phone_number]').val()
	}).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/warehouses/${response.data.id}`

	})
}
</script>
<?= $this->endSection() ?>