<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>
<?= $this->include('components/success_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.settings.settings') ?>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.settings.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="logo_upload" class="d-block"><?= lang('Main.settings.logo') ?></label>
								<div class="input-group">
									<div class="custom-file">
										<input type="file" class="custom-file-input" id="logo_upload" />
										<label class="custom-file-label" for="logo_upload"><?= lang('Main.settings.logo_choose_file') ?></label>
									</div>
								</div>
								<small class="form-text text-muted">
									<?= lang('Main.settings.logo_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="site_title" class="d-block"><?= lang('Main.settings.site_title') ?></label>
								<input type="text" name="site_title" id="site_title" class="form-control" value="<?= $settings->site_title ?>" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.settings.site_title_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="jwt_secret_key" class="d-block"><?= lang('Main.settings.jwt_secret_key') ?></label>
								<div class="input-group">
									<input type="text" id="jwt_secret_key" name="jwt_secret_key" class="form-control" value="<?= $settings->jwt_secret_key ?>" />
									<div class="input-group-append">
										<button type="button" onclick="generateJWTSecretKey()" class="btn btn-primary">
											<i class="fas fa-sync-alt"></i>
										</button>
									</div>
									<div class="invalid-feedback"></div>
								</div>
								
								<small class="form-text text-muted">
									<?= lang('Main.settings.jwt_secret_key_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="jwt_exp" class="d-block"><?= lang('Main.settings.jwt_exp') ?></label>
								<input type="text" name="jwt_exp" id="jwt_exp" class="form-control" value="<?= $settings->jwt_exp ?>" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.settings.jwt_exp_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="default_locale" class="d-block"><?= lang('Main.settings.default_locale') ?></label>
								<select name="default_locale" id="default_locale" class="custom-select">
									<?php
									foreach($locales as $locale) {
										if($settings->default_locale == $locale)
											echo "<option value=\"{$locale}\" selected>{$locale}</option>";
										else
											echo "<option value=\"{$locale}\">{$locale}</option>";
									}
									?>
								</select>
							</div>

							<div class="form-group">
								<label for="currency" class="d-block"><?= lang('Main.settings.currency') ?></label>
								<select name="currency" id="currency" class="custom-select">
									<?php
									foreach($currencies as $currency) {
										if($settings->currency_name == $currency->name)
											echo "<option value=\"{$currency->id}\" data-currency-name=\"{$currency->name}\" data-currency-symbol=\"{$currency->symbol}\" selected>{$currency->name} ({$currency->symbol})</option>";
										else
											echo "<option value=\"{$currency->id}\" data-currency-name=\"{$currency->name}\" data-currency-symbol=\"{$currency->symbol}\">{$currency->name} ({$currency->symbol})</option>";
									}
									?>
								</select>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.settings.references_generation') ?>
							</h6>

							<div class="form-group">
								<label for="references_style" class="d-block"><?= lang('Main.settings.style') ?></label>
								<select name="references_style" id="references_style" class="custom-select">
									<option value="increasing"<?php if($settings->references_style == 'increasing') echo ' selected' ?>><?= lang('Main.settings.style_increasing') ?></option>
									<option value="random"<?php if($settings->references_style == 'random') echo ' selected' ?>><?= lang('Main.settings.style_random') ?></option>
								</select>

								<small class="form-text text-muted">
									<?= lang('Main.settings.style_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="references_increasing_length" class="d-block"><?= lang('Main.settings.increasing_length') ?></label>
								<input type="text" name="references_increasing_length" id="references_increasing_length" class="form-control" value="<?= $settings->references_increasing_length ?>" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.settings.increasing_length_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="references_random_chars" class="d-block"><?= lang('Main.settings.allowed_characters') ?></label>
								<input type="text" name="references_random_chars" id="references_random_chars" class="form-control" value="<?= $settings->references_random_chars ?>" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.settings.allowed_characters_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="references_random_chars_length" class="d-block"><?= lang('Main.settings.random_chars_length') ?></label>
								<input type="text" name="references_random_chars_length" id="references_random_chars_length" class="form-control" value="<?= $settings->references_random_chars_length ?>" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.settings.random_chars_length_help') ?>
								</small>
							</div>

							<hr class="mt-5" />

							<h6 class="h6-5 pt-2 text-secondary">
								<?= lang('Main.settings.references_variations') ?>
							</h6>

							<small class="form-text text-muted mb-4">
								<?= lang('Main.settings.references_variations_help') ?>
							</small>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="references_sale_prepend" class="d-block"><?= lang('Main.settings.sale_prepend') ?></label>
										<input type="text" id="references_sale_prepend" name="references_sale_prepend" class="form-control" value="<?= $settings->references_sale_prepend ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="references_sale_append" class="d-block"><?= lang('Main.settings.sale_append') ?></label>
										<input type="text" id="references_sale_append" name="references_sale_append" class="form-control" value="<?= $settings->references_sale_append ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="references_purchase_prepend" class="d-block"><?= lang('Main.settings.purchase_prepend') ?></label>
										<input type="text" id="references_purchase_prepend" name="references_purchase_prepend" class="form-control" value="<?= $settings->references_purchase_prepend ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="references_purchase_append" class="d-block"><?= lang('Main.settings.purchase_append') ?></label>
										<input type="text" id="references_purchase_append" name="references_purchase_append" class="form-control" value="<?= $settings->references_purchase_append ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<hr class="mt-4" />

							<h6 class="h6-5 pt-2 text-secondary">
								<?= lang('Main.settings.return_references_variations') ?>
							</h6>

							<small class="form-text text-muted mb-4">
								<?= lang('Main.settings.return_references_variations_help') ?>
							</small>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="references_sale_return_prepend" class="d-block"><?= lang('Main.settings.sale_return_prepend') ?></label>
										<input type="text" id="references_sale_return_prepend" name="references_sale_return_prepend" class="form-control" value="<?= $settings->references_sale_return_prepend ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="references_sale_return_append" class="d-block"><?= lang('Main.settings.sale_return_append') ?></label>
										<input type="text" id="references_sale_return_append" name="references_sale_return_append" class="form-control" value="<?= $settings->references_sale_return_append ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="references_purchase_return_prepend" class="d-block"><?= lang('Main.settings.purchase_return_prepend') ?></label>
										<input type="text" id="references_purchase_return_prepend" name="references_purchase_return_prepend" class="form-control" value="<?= $settings->references_purchase_return_prepend ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="references_purchase_return_append" class="d-block"><?= lang('Main.settings.purchase_return_append') ?></label>
										<input type="text" id="references_purchase_return_append" name="references_purchase_return_append" class="form-control" value="<?= $settings->references_purchase_return_append ?>" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.settings.save') ?>
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

		changeVisibilities(0)

		// When changing references style
		$('select[name=references_style]').on('change', e => {
			changeVisibilities(200)
		})

		// When uploading a new logo
		$('input#logo_upload').on('change', e => {
			let filename = $('input#logo_upload').val().split('\\').pop()
			if(filename == '') {
				$('input#logo_upload').next('label').html("<?= langSlashes('Main.settings.logo_choose_file') ?>")
				return
			}

			$('input#logo_upload').next('label').html(filename)

			let formData = new FormData()
			var file = document.querySelector('#logo_upload')
			formData.append('logo', file.files[0])
			axios.post(`api/settings/upload-logo`, formData, {
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			}).then(response => {
				location.reload()
			})
		})

		$('form').on('submit', e => {
			e.preventDefault()

			let validator = new Validator()
			validator.addInputTextVal('site_title', 'minLength', 1, "<?= langSlashes('Validation.settings.site_title_min_length') ?>")
			validator.addInputTextVal('jwt_secret_key', 'minLength', 20, "<?= langSlashes('Validation.settings.jwt_secret_key_min_length') ?>")
			validator.addInputText('jwt_exp', 'integer', "<?= langSlashes('Validation.settings.jwt_exp_numeric') ?>")
			validator.addInputText('references_increasing_length', 'integer', '<?= langSlashes('references_increasing_length_numeric') ?>')
			validator.addInputTextVal('references_random_chars', 'minLength', 1, "<?= langSlashes('references_random_chars_min_length') ?>")
			validator.addInputText('references_random_chars_length', 'integer', '<?= langSlashes('references_random_chars_length_numeric') ?>')
			validator.addInputTextVal('references_random_chars_length', 'minValue', 4, '<?= langSlashes('references_random_chars_length_greater_than_equal_to') ?>')

			if(validator.validate())
				submitSettings()
		})
	})
})(jQuery)

function submitSettings() {
	axios.put('api/settings', {
		references_style: $('select[name=references_style]').val(),
		references_increasing_length: $('input[name=references_increasing_length]').val(),
		references_random_chars: $('input[name=references_random_chars]').val(),
		references_random_chars_length: $('input[name=references_random_chars_length]').val(),
		references_sale_append: $('input[name=references_sale_append]').val(),
		references_sale_prepend: $('input[name=references_sale_prepend]').val(),
		references_purchase_append: $('input[name=references_purchase_append]').val(),
		references_purchase_prepend: $('input[name=references_purchase_prepend]').val(),
		references_purchase_return_append: $('input[name=references_purchase_return_append]').val(),
		references_purchase_return_prepend: $('input[name=references_purchase_return_prepend]').val(),
		references_sale_return_append: $('input[name=references_sale_return_append]').val(),
		references_sale_return_prepend: $('input[name=references_sale_return_prepend]').val(),
		jwt_secret_key: $('input[name=jwt_secret_key]').val(),
		jwt_exp: $('input[name=jwt_exp]').val(),
		site_title: $('input[name=site_title]').val(),
		default_locale: $('select[name=default_locale]').val(),
		currency_name: $('select[name=currency]').find(':selected').data('currency-name'),
		currency_symbol: $('select[name=currency]').find(':selected').data('currency-symbol')
	}).then(response => {
		console.log(response)
		showSuccess("Success", "Settings saved successfully")
	})
}

function changeVisibilities(ms) {
	let references_style = $('select[name=references_style]').val()
	if(references_style == 'increasing') {
		$('input[name=references_increasing_length]').parent().slideDown(ms)
		$('input[name=references_random_chars]').parent().slideUp(ms)
		$('input[name=references_random_chars_length]').parent().slideUp(ms)
	}else{
		$('input[name=references_increasing_length]').parent().slideUp(ms)
		$('input[name=references_random_chars]').parent().slideDown(ms)
		$('input[name=references_random_chars_length]').parent().slideDown(ms)
	}
}

function generateJWTSecretKey() {
	axios.get('api/settings/random-jwt').then(response => {
		$('input[name=jwt_secret_key]').val(response.data.jwt)
	})
}
</script>
<?= $this->endSection() ?>