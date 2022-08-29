<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.items.new_item') ?>
			</div>

			<div class="content">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.items.item_name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="code_type" class="d-block"><?= lang('Main.items.barcode_type') ?></label>
								<select name="code_type" id="code_type" class="custom-select">
									<option value="none"><?= lang('Main.items.none') ?></option>
									<option value="code39"><?= lang('Main.items.code39') ?></option>
									<option value="code128"><?= lang('Main.items.code128') ?></option>
									<option value="ean-8"><?= lang('Main.items.ean8') ?></option>
									<option value="ean-13"><?= lang('Main.items.ean13') ?></option>
									<option value="upc-a"><?= lang('Main.items.upca') ?></option>
									<option value="qr"><?= lang('Main.items.qr') ?></option>
								</select>

								<small class="form-text text-muted">
									<?= lang('Main.items.barcode_type_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="code" class="d-block"><?= lang('Main.items.code') ?>*</label>
								<div class="input-group">
									<input type="text" id="code" name="code" class="form-control" />
									<div class="input-group-append">
										<button type="button" onclick="generateCode()" class="btn btn-primary">
											<i class="fas fa-sync-alt"></i>
										</button>
									</div>
									<div class="invalid-feedback"></div>
								</div>
								
								<small class="form-text text-muted">
									<?= lang('Main.items.code_help.none') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="brand" class="d-block"><?= lang('Main.items.brand') ?></label>
								<select name="brand" id="brand" class="custom-select">
									<option value=""><?= lang('Main.items.none') ?></option>
									<?php foreach($brands as $brand) { ?>
									<option value="<?= $brand->id ?>"><?= $brand->name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="form-group">
								<label for="category" class="d-block"><?= lang('Main.items.category') ?></label>
								<select name="category" id="category" class="custom-select">
									<option value=""><?= lang('Main.items.none') ?></option>
									<?php foreach($categories as $category) { ?>
									<option value="<?= $category->id ?>"><?= $category->name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="sale_price" class="d-block"><?= lang('Main.items.sale_price') ?>*</label>
										<input type="text" id="sale_price" name="sale_price" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="sale_tax" class="d-block"><?= lang('Main.items.sale_tax_percent') ?></label>
										<input type="text" id="sale_tax" name="sale_tax" class="form-control" value="0" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="description" class="d-block"><?= lang('Main.items.description') ?></label>
								<textarea id="description" name="description" rows="5" wrap="soft" class="form-control"></textarea>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.dimensions') ?>
							</h6>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="weight" class="d-block"><?= lang('Main.items.weight_kg') ?></label>
										<input type="text" id="weight" name="weight" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="width" class="d-block"><?= lang('Main.items.width_m') ?></label>
										<input type="text" id="width" name="width" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="height" class="d-block"><?= lang('Main.items.height_m') ?></label>
										<input type="text" id="height" name="height" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="depth" class="d-block"><?= lang('Main.items.depth_m') ?></label>
										<input type="text" id="depth" name="depth" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<hr class="mt-3 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.alerts') ?>
							</h6>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="min_alert" class="d-block"><?= lang('Main.items.minimum_qty_alert') ?></label>
										<input type="text" id="min_alert" name="min_alert" class="form-control" />
										<div class="invalid-feedback"></div>
										<small class="form-text text-muted">
											<?= lang('Main.items.minimum_qty_alert_help') ?>
										</small>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="max_alert" class="d-block"><?= lang('Main.items.maximum_qty_alert') ?></label>
										<input type="text" id="max_alert" name="max_alert" class="form-control" />
										<div class="invalid-feedback"></div>
										<small class="form-text text-muted">
											<?= lang('Main.items.maximum_qty_alert_help') ?>
										</small>
									</div>
								</div>
							</div>

							<hr class="mt-3 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.notes') ?>
							</h6>

							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.items.notes') ?></label>
								<textarea id="notes" name="notes" rows="5" wrap="soft" class="form-control"></textarea>
							</div>

						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.items.create') ?>
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
	
	let code_requirements = [
		{ type: 'none', text: "<?= langSlashes('Main.items.code_help.none') ?>" },
		{ type: 'code39', text: "<?= langSlashes('Main.items.code_help.code39') ?>" },
		{ type: 'code128', text: "<?= langSlashes('Main.items.code_help.code128') ?>" },
		{ type: 'ean-8', text: "<?= langSlashes('Main.items.code_help.ean8') ?>" },
		{ type: 'ean-13', text: "<?= langSlashes('Main.items.code_help.ean13') ?>" },
		{ type: 'upc-a', text: "<?= langSlashes('Main.items.code_help.upca') ?>" },
		{ type: 'qr', text: "<?= langSlashes('Main.items.code_help.qr') ?>" }
	]

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		$('select[name=code_type]').on('change', e => {
			let val = $(e.currentTarget).val()

			code_requirements.forEach(requirement => {
				if(requirement.type == val)
					$('input[name=code]').parent().parent().children('.text-muted').text(requirement.text)
			})
		})

		// Prevent form submission when hitting enter in the "code" input
		$('input[name=code]').on('keypress', e => {
			if(e.which == 13)
				e.preventDefault()
		})

		$('form').on('submit', e => {
			e.preventDefault()

			let validator = new Validator()
			validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.items.name_min_length') ?>")
			validator.addInputTextVal('name', 'maxLength', 45, "<?= langSlashes('Validation.items.name_max_length') ?>")
			validator.addInputTextVal('code', 'minLength', 1, "<?= langSlashes('Validation.items.code_min_length') ?>")
			validator.addInputTextCustom('code', value => {
				let barcode_type = $('select[name=code_type]').val()

				let barcode_rules = {
					'none': /^[ -~]{1,500}$/,
					'code39': /^[A-Z0-9\s-.$/+%]+$/,
					'code128': /^[ -~]{1,128}$/,
					'ean-8': /^\d{8}$/,
					'ean-13': /^\d{13}$/,
					'upc-a': /^\d{12}$/,
					'qr': /^[ -~]{1,500}$/
				}
				if(barcode_rules[barcode_type].test(value))
					return true
				return false
			}, "<?= langSlashes('Validation.items.code_invalid_frontend') ?>")
			validator.addInputText('sale_price', 'decimal', "<?= langSlashes('Validation.items.sale_price_greater_than') ?>")
			validator.addInputTextVal('sale_price', 'minValue', 0.01, "<?= langSlashes('Validation.items.sale_price_greater_than') ?>")
			validator.addInputText('sale_tax', 'decimal', "<?= langSlashes('Validation.items.sale_tax_greater_than_equal_to') ?>")
			validator.addInputTextVal('sale_tax', 'minValue', 0, "<?= langSlashes('Validation.items.sale_tax_greater_than_equal_to') ?>")
			validator.addInputText('weight', 'optional-decimal', "<?= langSlashes('Validation.items.weight_decimal') ?>")
			validator.addInputText('width', 'optional-decimal', "<?= langSlashes('Validation.items.width_decimal') ?>")
			validator.addInputText('height', 'optional-decimal', "<?= langSlashes('Validation.items.height_decimal') ?>")
			validator.addInputText('depth', 'optional-decimal', "<?= langSlashes('Validation.items.depth_decimal') ?>")
			validator.addInputText('min_alert', 'optional-integer', "<?= langSlashes('Validation.items.min_alert_greater_than_equal_to') ?>")
			validator.addInputText('max_alert', 'optional-integer', "<?= langSlashes('Validation.items.max_alert_greater_than_equal_to') ?>")
			validator.addInputTextCustom('max_alert', value => {
				if(value != '' && value != null) {
					if(value < 1)
						return false
				}
				return true
			}, "<?= langSlashes('Validation.items.max_alert_greater_than_equal_to') ?>")

			if(validator.validate())
				createItem()
		})
	})
})(jQuery)

function createItem() {
	axios.post('api/items', {
		name: $('input[name=name]').val(),
		code: $('input[name=code]').val(),
		code_type: $('select[name=code_type]').val(),
		sale_price: $('input[name=sale_price]').val(),
		sale_tax: $('input[name=sale_tax]').val(),
		description: $('textarea[name=description]').val(),
		weight: $('input[name=weight]').val(),
		width: $('input[name=width]').val(),
		height: $('input[name=height]').val(),
		depth: $('input[name=depth]').val(),
		min_alert: $('input[name=min_alert]').val(),
		max_alert: $('input[name=max_alert]').val(),
		notes: $('textarea[name=notes]').val(),
		category_id: $('select[name=category]').val(),
		brand_id: $('select[name=brand]').val()
	}).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/items/${response.data.id}`
	})
}

function generateCode() {
	let codeType = $('select[name=code_type]').val()

	axios.get(`api/items/generate-code/${codeType}`).then(response => {
		$('input[name=code]').val(response.data.code)
	})
}
</script>
<?= $this->endSection() ?>