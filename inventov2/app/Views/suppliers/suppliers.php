<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('suppliers/modals/supplier_modal') ?>
<?= $this->include('suppliers/modals/edit_supplier_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Items -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.suppliers.suppliers') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/suppliers/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('suppliers/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.suppliers.new_supplier') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="suppliers" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.suppliers.name') ?></th>
								<th><?= lang('Main.suppliers.internal_name') ?></th>
								<th><?= lang('Main.suppliers.company_name') ?></th>
								<th><?= lang('Main.suppliers.email_address') ?></th>
								<th><?= lang('Main.suppliers.phone_number') ?></th>
								<th><?= lang('Main.suppliers.vat') ?></th>
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
	
var openSupplier = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#suppliers').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#suppliers').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/suppliers') ?>",
			columns: [
				{ data: "name" },
				{ data: "internal_name" },
				{ data: "company_name" },
				{ data: "email_address" },
				{ data: "phone_number" },
				{ data: "vat" }
			]
		})

		$('table#suppliers tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadSupplier(id)
		})

		$('#supplierModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/suppliers`)
		})

		$('#editSupplierModal').on('hide.bs.modal', e => {
			loadSupplier(openSupplier.id)
		})

		$('#editSupplierModal').on('show.bs.modal', e => {
			$('#supplierModal').modal('hide')
		})

		<?php if($supplierId != false) { ?>
		loadSupplier(<?= $supplierId ?>)
		<?php } ?>
		
		$('#editSupplierModal form').on('submit', e => {
			e.preventDefault()
			editSupplierSubmit()
		})
	})
})(jQuery)

function loadSupplier(id) {
	axios.get(`api/suppliers/${id}`).then(response => {
		let supplier = response.data

		openSupplier = supplier

		window.history.pushState(null, '', `<?= base_url() ?>/suppliers/${id}`)

		$('#supplierModal').modal('show')

		$('#supplierModal td[data-item-field=id]').text(supplier.id)
		$('#supplierModal td[data-item-field=name]').text(supplier.name)
		$('#supplierModal td[data-item-field=internal_name]').text(supplier.internal_name)
		$('#supplierModal td[data-item-field=company_name]').text(supplier.company_name)
		$('#supplierModal td[data-item-field=vat]').text(supplier.vat)
		$('#supplierModal td[data-item-field=email_address]').text(supplier.email_address)
		$('#supplierModal td[data-item-field=phone_number]').text(supplier.phone_number)
		$('#supplierModal td[data-item-field=address]').text(supplier.address)
		$('#supplierModal td[data-item-field=city]').text(supplier.city)
		$('#supplierModal td[data-item-field=country]').text(supplier.country)
		$('#supplierModal td[data-item-field=state]').text(supplier.state)
		$('#supplierModal td[data-item-field=zip_code]').text(supplier.zip_code)
		$('#supplierModal td[data-item-field=created_by]').text(supplier.created_by.name)
		$('#supplierModal td[data-item-field=created_at]').text(supplier.created_at)
		$('#supplierModal td[data-item-field=custom_field_1]').text(supplier.custom_field1)
		$('#supplierModal td[data-item-field=custom_field_2]').text(supplier.custom_field2)
		$('#supplierModal td[data-item-field=custom_field_3]').text(supplier.custom_field3)
		$('#supplierModal td[data-item-field=notes]').text(supplier.notes)
	})
}

function editSupplier() {
	$('#editSupplierModal input[name=id]').val(openSupplier.id)
	$('#editSupplierModal input[name=name]').val(openSupplier.name)
	$('#editSupplierModal input[name=internal_name]').val(openSupplier.internal_name)
	$('#editSupplierModal input[name=company_name]').val(openSupplier.company_name)
	$('#editSupplierModal input[name=vat]').val(openSupplier.vat)
	$('#editSupplierModal input[name=email_address]').val(openSupplier.email_address)
	$('#editSupplierModal input[name=phone_number]').val(openSupplier.phone_number)
	$('#editSupplierModal input[name=address]').val(openSupplier.address)
	$('#editSupplierModal input[name=city]').val(openSupplier.city)
	$('#editSupplierModal input[name=country]').val(openSupplier.country)
	$('#editSupplierModal input[name=state]').val(openSupplier.state)
	$('#editSupplierModal input[name=zip_code]').val(openSupplier.zip_code)
	$('#editSupplierModal input[name=created_by]').val(openSupplier.created_by.name)
	$('#editSupplierModal input[name=created_at]').val(openSupplier.created_at)
	$('#editSupplierModal input[name=custom_field_1]').val(openSupplier.custom_field1)
	$('#editSupplierModal input[name=custom_field_2]').val(openSupplier.custom_field2)
	$('#editSupplierModal input[name=custom_field_3]').val(openSupplier.custom_field3)
	$('#editSupplierModal input[name=notes]').val(openSupplier.notes)
	
	$('#editSupplierModal').modal('show')
}

function editSupplierSubmit() {
	let validator = new Validator()
	validator.addInputTextVal('name', 'minLength', 1, "<?= langSlashes('Validation.suppliers.name_min_length') ?>")
	validator.addInputTextVal('name', 'maxLength', 45, "<?= langSlashes('Validation.suppliers.name_max_length') ?>")
	validator.addInputTextVal('internal_name', 'maxLength', 45, "<?= langSlashes('Validation.suppliers.internal_name_max_length') ?>")
	validator.addInputTextVal('company_name', 'maxLength', 100, "<?= langSlashes('Validation.suppliers.company_name_max_length') ?>")
	validator.addInputTextVal('vat', 'maxLength', 45, "<?= langSlashes('Validation.suppliers.vat_max_length') ?>")
	validator.addInputText('email_address', 'optional-email-address', "<?= langSlashes('Validation.suppliers.email_address_invalid') ?>")
	validator.addInputTextVal('phone_number', 'maxLength', 20, "<?= langSlashes('Validation.suppliers.phone_number_max_length') ?>")
	validator.addInputTextVal('address', 'maxLength', 80, "<?= langSlashes('Validation.suppliers.address_max_length') ?>")
	validator.addInputTextVal('city', 'maxLength', 80, "<?= langSlashes('Validation.suppliers.city_max_length') ?>")
	validator.addInputTextVal('country', 'maxLength', 30, "<?= langSlashes('Validation.suppliers.country_max_length') ?>")
	validator.addInputTextVal('state', 'maxLength', 30, "<?= langSlashes('Validation.suppliers.state_max_length') ?>")
	validator.addInputText('zip_code', 'optional-integer', "<?= langSlashes('Validation.suppliers.zip_code_invalid') ?>")
	validator.addInputTextVal('zip_code', 'maxLength', 12, "<?= langSlashes('Validation.suppliers.zip_code_max_length') ?>")

	if(!validator.validate())
		return

	axios.put(`api/suppliers/${openSupplier.id}`, {
		id: $('input[name=id]').val(),
		name: $('input[name=name]').val(),
		internal_name: $('input[name=internal_name]').val(),
		company_name: $('input[name=company_name]').val(),
		vat: $('input[name=vat]').val(),
		email_address: $('input[name=email_address]').val(),
		phone_number: $('input[name=phone_number]').val(),
		address: $('input[name=address]').val(),
		city: $('input[name=city]').val(),
		country: $('input[name=country]').val(),
		state: $('input[name=state]').val(),
		zip_code: $('input[name=zip_code]').val(),
		created_by: $('input[name=created_by]').val(),
		created_at: $('input[name=created_at]').val(),
		custom_field1: $('input[name=custom_field_1]').val(),
		custom_field2: $('input[name=custom_field_2]').val(),
		custom_field3: $('input[name=custom_field_3]').val(),
		notes: $('input[name=notes]').val()
	}).then(response => {
		$('#editSupplierModal').modal('hide')
		table.ajax.reload()
	})
}

function deleteSupplier() {
	showConfirmation('<?= langSlashes('Main.suppliers.delete_confirmation.title') ?>',
		'<?= langSlashes('Main.suppliers.delete_confirmation.msg') ?>',
		'<?= langSlashes('Main.suppliers.delete_confirmation.yes') ?>',
		'<?= langSlashes('Main.suppliers.delete_confirmation.no') ?>',
		() => {
			deleteSupplierSubmit()
			return true
		},
		() => {
			return true
		})
}

function deleteSupplierSubmit() {
	axios.delete(`api/suppliers/${openSupplier.id}`).then(response => {
		$('#supplierModal').modal('hide')
		table.ajax.reload()

	})
}
</script>
<?= $this->endSection() ?>