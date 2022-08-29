<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('categories/modals/category_modal') ?>
<?= $this->include('categories/modals/edit_category_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Items -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.categories.categories') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/categories/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('categories/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.categories.new_category') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="categories" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.categories.name') ?></th>
								<th><?= lang('Main.categories.created_by') ?></th>
								<th><?= lang('Main.categories.created_at') ?></th>
								<th><?= lang('Main.categories.items_registered') ?></th>
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

var openCategory = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#categories').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#categories').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/categories') ?>",
			columns: [
				{ data: "name" },
				{ data: "created_by_name" },
				{ data: "created_at" },
				{ data: "items" }
			]
		})

		$('table#categories tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadCategory(id)
		})

		$('#categoryModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/categories`)
		})

		$('#editCategoryModal').on('hide.bs.modal', e => {
			loadCategory(openCategory.id)
		})

		$('#editCategoryModal').on('show.bs.modal', e => {
			$('#categoryModal').modal('hide')
		})

		<?php if($categoryId != false) { ?>
		loadCategory(<?= $categoryId ?>)
		<?php } ?>
		
		$('#editCategoryModal form').on('submit', e => {
			e.preventDefault()
			editCategorySubmit()
		})
	})
})(jQuery)

function loadCategory(id) {
	axios.get(`api/categories/${id}`).then(response => {
		let category = response.data

		openCategory = category

		window.history.pushState(null, '', `<?= base_url() ?>/categories/${id}`)

		$('#categoryModal').modal('show')

		$('#categoryModal td[data-item-field=id]').text(category.id)
		$('#categoryModal td[data-item-field=name]').text(category.name)
		$('#categoryModal td[data-item-field=description]').text(category.description)
		$('#categoryModal td[data-item-field=created_by]').text(category.created_by.name)
		$('#categoryModal td[data-item-field=created_at]').text(category.created_at)
	})
}

function editCategory() {
	$('#editCategoryModal input[name=name]').val(openCategory.name)
	$('#editCategoryModal textarea[name=description]').val(openCategory.description)
	
	$('#editCategoryModal').modal('show')
}

function editCategorySubmit() {
	let validator = new Validator()
	validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.categories.name_min_length') ?>")
	validator.addInputTextVal('name', 'maxLength', 100, "<?= langSlashes('Validation.categories.name_max_length') ?>")

	if(!validator.validate())
		return

	axios.put(`api/categories/${openCategory.id}`, {
		name: $('input[name=name]').val(),
		description: $('textarea[name=description]').val()
	}).then(response => {
		$('#editCategoryModal').modal('hide')
		table.ajax.reload()
		
	})
}

function deleteCategory() {
	showConfirmation('<?= langSlashes('Main.categories.delete_confirmation.title') ?>',
		'<?= langSlashes('Main.categories.delete_confirmation.msg') ?>',
		'<?= langSlashes('Main.categories.delete_confirmation.yes') ?>',
		'<?= langSlashes('Main.categories.delete_confirmation.no') ?>',
		() => {
			deleteCategorySubmit()
			return true
		},
		() => {
			return true
		})
}

function deleteCategorySubmit() {
	axios.delete(`api/categories/${openCategory.id}`).then(response => {
		$('#categoryModal').modal('hide')
		table.ajax.reload()
		
	})
}
</script>
<?= $this->endSection() ?>