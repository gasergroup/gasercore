<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('brands/modals/brand_modal') ?>
<?= $this->include('brands/modals/edit_brand_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Items -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.brands.brands') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/brands/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('brands/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.brands.new_brand') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="brands" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.brands.name') ?></th>
								<th><?= lang('Main.brands.created_by') ?></th>
								<th><?= lang('Main.brands.created_at') ?></th>
								<th><?= lang('Main.brands.items_registered') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Items -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

let openBrand = {};
let table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#brands').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#brands').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/brands') ?>",
			columns: [
				{ data: "name" },
				{ data: "created_by_name" },
				{ data: "created_at" },
				{ data: "items" }
			]
		})

		$('table#brands tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadBrand(id)
		})

		$('#brandModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/brands`)
		})

		$('#editBrandModal').on('hide.bs.modal', e => {
			loadBrand(openBrand.id)
		})

		$('#editBrandModal').on('show.bs.modal', e => {
			$('#brandModal').modal('hide')
		})

		<?php if($brandId != false) { ?>
		loadBrand(<?= $brandId ?>)
		<?php } ?>
		
		$('#editBrandModal form').on('submit', e => {
			e.preventDefault()
			editBrandSubmit()
		})
	})
})(jQuery)

function loadBrand(id) {
	axios.get(`api/brands/${id}`).then(response => {
		let brand = response.data

		openBrand = brand

		window.history.pushState(null, '', `<?= base_url() ?>/brands/${id}`)

		$('#brandModal').modal('show')

		$('#brandModal td[data-item-field=id]').text(brand.id)
		$('#brandModal td[data-item-field=name]').text(brand.name)
		$('#brandModal td[data-item-field=description]').text(brand.description)
		$('#brandModal td[data-item-field=created_by]').text(brand.created_by.name)
		$('#brandModal td[data-item-field=created_at]').text(brand.created_at)
	})
}

function editBrand() {
	$('#editBrandModal input[name=name]').val(openBrand.name)
	$('#editBrandModal textarea[name=description]').val(openBrand.description)
	
	$('#editBrandModal').modal('show')
}

function editBrandSubmit() {
	let validator = new Validator()
	validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.brands.name_min_length') ?>")
	validator.addInputTextVal('name', 'maxLength', 100, "<?= langSlashes('Validation.brands.name_max_length') ?>")

	if(!validator.validate())
		return

	axios.put(`api/brands/${openBrand.id}`, {
		name: $('input[name=name]').val(),
		description: $('textarea[name=description]').val()
	}).then(response => {
		$('#editBrandModal').modal('hide')
		table.ajax.reload()
		
	})
}

function deleteBrand() {
	showConfirmation('<?= langSlashes('Main.brands.delete_confirmation.title') ?>',
		'<?= langSlashes('Main.brands.delete_confirmation.msg') ?>',
		'<?= langSlashes('Main.brands.delete_confirmation.yes') ?>',
		'<?= langSlashes('Main.brands.delete_confirmation.no') ?>',
		() => {
			deleteBrandSubmit()
			return true
		},
		() => {
			return true
		})
}

function deleteBrandSubmit() {
	axios.delete(`api/brands/${openBrand.id}`).then(response => {
		$('#brandModal').modal('hide')
		table.ajax.reload()
		
	})
}
</script>
<?= $this->endSection() ?>