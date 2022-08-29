<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>

<?= $this->include('components/error_modal'); ?>

<div class="row">
	<div class="px-2 mt-n1 col">
		<div class="section">
			<div class="header">
				<?= lang('Main.categories.new_category') ?>
			</div>

			<div class="content">
			<form>
					<h6 class="h6-5 text-secondary mb-3">
						<?= lang('Main.categories.basic_information') ?>
					</h6>

					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.categories.category_name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="description" class="d-block"><?= lang('Main.categories.description') ?></label>
								<textarea id="description" name="description" rows="5" class="form-control"></textarea>
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.categories.create_category') ?>
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

			validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.categories.name_min_length') ?>")
			validator.addInputTextVal('name', 'maxLength', 100, "<?= langSlashes('Validation.categories.name_max_length') ?>")

			if(validator.validate())
				createCategory()
		})
	})
})(jQuery)

function createCategory() {
	axios.post('api/categories', {
		name: $('input[name=name]').val(),
		description: $('textarea[name=description]').val()
	}).then(response => {
		if(response && response.data && response.data.id)
			location.href = `<?= base_url() ?>/categories/${response.data.id}`

	})
}
</script>
<?= $this->endSection() ?>