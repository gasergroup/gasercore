<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('customers/modals/customer_modal') ?>
<?= $this->include('customers/modals/edit_customer_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Items -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.customers.customers') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/customers/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('customers/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.customers.new_customer') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="customers" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.customers.name') ?></th>
								<th><?= lang('Main.customers.internal_name') ?></th>
								<th><?= lang('Main.customers.email_address') ?></th>
								<th><?= lang('Main.customers.phone_number') ?></th>
								<th><?= lang('Main.customers.tax_number') ?></th>
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

var openCustomer = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#customers').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#customers').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/customers') ?>",
			columns: [
				{ data: "name" },
				{ data: "internal_name" },
				{ data: "email_address" },
				{ data: "phone_number" },
				{ data: "tax_number" }
			]
		})

		$('table#customers tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadCustomer(id)
		})

		$('#customerModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/customers`)
		})

		$('#editCustomerModal').on('hide.bs.modal', e => {
			loadCustomer(openCustomer.id)
		})

		$('#editCustomerModal').on('show.bs.modal', e => {
			$('#customerModal').modal('hide')
		})

		<?php if($customerId != false) { ?>
		loadCustomer(<?= $customerId ?>)
		<?php } ?>
		
		$('#editCustomerModal form').on('submit', e => {
			e.preventDefault()
			editCustomerSubmit()
		})
	})
})(jQuery)

function loadCustomer(id) {
	axios.get(`api/customers/${id}`).then(response => {
		let customer = response.data

		openCustomer = customer

		window.history.pushState(null, '', `<?= base_url() ?>/customers/${id}`)

		$('#customerModal').modal('show')

		$('#customerModal td[data-item-field=id]').text(customer.id)
		$('#customerModal td[data-item-field=name]').text(customer.name)
		$('#customerModal td[data-item-field=internal_name]').text(customer.internal_name)
		$('#customerModal td[data-item-field=company_name]').text(customer.company_name)
		$('#customerModal td[data-item-field=tax_number]').text(customer.vat)
		$('#customerModal td[data-item-field=email_address]').text(customer.email_address)
		$('#customerModal td[data-item-field=phone_number]').text(customer.phone_number)
		$('#customerModal td[data-item-field=address]').text(customer.address)
		$('#customerModal td[data-item-field=city]').text(customer.city)
		$('#customerModal td[data-item-field=country]').text(customer.country)
		$('#customerModal td[data-item-field=state]').text(customer.state)
		$('#customerModal td[data-item-field=zip_code]').text(customer.zip_code)
		$('#customerModal td[data-item-field=created_by]').text(customer.created_by.name)
		$('#customerModal td[data-item-field=created_at]').text(customer.created_at)
		$('#customerModal td[data-item-field=custom_field_1]').text(customer.custom_field1)
		$('#customerModal td[data-item-field=custom_field_2]').text(customer.custom_field2)
		$('#customerModal td[data-item-field=custom_field_3]').text(customer.custom_field3)
		$('#customerModal td[data-item-field=notes]').text(customer.notes)
	})
}

function editCustomer() {
	$('#editCustomerModal input[name=id]').val(openCustomer.id)
	$('#editCustomerModal input[name=name]').val(openCustomer.name)
	$('#editCustomerModal input[name=internal_name]').val(openCustomer.internal_name)
	$('#editCustomerModal input[name=company_name]').val(openCustomer.company_name)
	$('#editCustomerModal input[name=tax_number]').val(openCustomer.vat)
	$('#editCustomerModal input[name=email_address]').val(openCustomer.email_address)
	$('#editCustomerModal input[name=phone_number]').val(openCustomer.phone_number)
	$('#editCustomerModal input[name=address]').val(openCustomer.address)
	$('#editCustomerModal input[name=city]').val(openCustomer.city)
	$('#editCustomerModal input[name=country]').val(openCustomer.country)
	$('#editCustomerModal input[name=state]').val(openCustomer.state)
	$('#editCustomerModal input[name=zip_code]').val(openCustomer.zip_code)
	$('#editCustomerModal input[name=created_by]').val(openCustomer.created_by.name)
	$('#editCustomerModal input[name=created_at]').val(openCustomer.created_at)
	$('#editCustomerModal input[name=custom_field_1]').val(openCustomer.custom_field1)
	$('#editCustomerModal input[name=custom_field_2]').val(openCustomer.custom_field2)
	$('#editCustomerModal input[name=custom_field_3]').val(openCustomer.custom_field3)
	$('#editCustomerModal input[name=notes]').val(openCustomer.notes)
	
	$('#editCustomerModal').modal('show')
}

function editCustomerSubmit() {
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

	if(!validator.validate())
		return

	axios.put(`api/customers/${openCustomer.id}`, {
		id: $('input[name=id]').val(),
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
		created_by: $('input[name=created_by]').val(),
		created_at: $('input[name=created_at]').val(),
		custom_field1: $('input[name=custom_field_1]').val(),
		custom_field2: $('input[name=custom_field_2]').val(),
		custom_field3: $('input[name=custom_field_3]').val(),
		notes: $('input[name=notes]').val()
	}).then(response => {
		$('#editCustomerModal').modal('hide')
		table.ajax.reload()
	})
}

function deleteCustomer() {
	showConfirmation("<?= langSlashes('Main.customers.delete_confirmation.title') ?>",
		"<?= langSlashes('Main.customers.delete_confirmation.msg') ?>",
		"<?= langSlashes('Main.customers.delete_confirmation.yes') ?>",
		"<?= langSlashes('Main.customers.delete_confirmation.no') ?>",
		() => {
			deleteCustomerSubmit()
			return true
		},
		() => {
			return true
		})
}

function deleteCustomerSubmit() {
	axios.delete(`api/customers/${openCustomer.id}`).then(response => {
		$('#customerModal').modal('hide')
		table.ajax.reload()
		
	})
}
</script>
<?= $this->endSection() ?>