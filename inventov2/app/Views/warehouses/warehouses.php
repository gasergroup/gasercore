<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('warehouses/modals/warehouse_modal') ?>
<?= $this->include('warehouses/modals/edit_warehouse_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Warehouses -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.warehouses.warehouses') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/warehouses/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('warehouses/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.warehouses.new_warehouse') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="warehouses" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.warehouses.name') ?></th>
								<th><?= lang('Main.warehouses.address') ?></th>
								<th><?= lang('Main.warehouses.phone_number') ?></th>
								<th><?= lang('Main.warehouses.total_qty') ?></th>
								<th><?= lang('Main.warehouses.total_value_sale_price') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Warehouses -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';

var openWarehouse = {};
var table = {};

(function($) {
	'use strict';
	
	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#warehouses').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#warehouses').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/warehouses') ?>",
			columns: [
				{ data: "name" },
				{ data: "address" },
				{ data: "phone_number" },
				{ data: "total_qty" },
				{ data: "total_value" }
			]
		})

		$('table#warehouses tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadWarehouse(id)
		})

		$('#warehouseModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/warehouses`)
		})

		$('#editWarehouseModal').on('hide.bs.modal', e => {
			loadWarehouse(openWarehouse.id)
		})

		$('#editWarehouseModal').on('show.bs.modal', e => {
			$('#warehouseModal').modal('hide')
		})

		<?php if($warehouseId != false) { ?>
		loadWarehouse(<?= $warehouseId ?>)
		<?php } ?>

		// When selecting a worker/supervisor to add it to a warehouse
		$('select[name=add_worker], select[name=add_supervisor]').on('change', e => {
			let newUserId = $(e.currentTarget).val()

			axios.put(`api/users/${newUserId}/add-warehouse/${openWarehouse.id}`).then(response => {
				loadWarehouse(openWarehouse.id)
			})
		})

		$('#editWarehouseModal form').on('submit', e => {
			e.preventDefault()
			editWarehouseSubmit()
		})
	})
})(jQuery)

function loadWarehouse(id) {
	axios.get(`api/warehouses/${id}`).then(response => {
		let warehouse = response.data

		openWarehouse = warehouse

		window.history.pushState(null, '', `<?= base_url() ?>/warehouses/${id}`)

		$('#warehouseModal').modal('show')

		$('#warehouseModal td[data-item-field="id"]').text(warehouse.id)
		$('#warehouseModal td[data-item-field="name"]').text(warehouse.name)
		$('#warehouseModal td[data-item-field="phone_number"]').text(warehouse.phone_number)
		$('#warehouseModal td[data-item-field="created_by"]').text(warehouse.created_by.name)
		$('#warehouseModal td[data-item-field="created_at"]').text(warehouse.created_at)
		$('#warehouseModal td[data-item-field="address"]').text(warehouse.address)
		$('#warehouseModal td[data-item-field="city"]').text(warehouse.city)
		$('#warehouseModal td[data-item-field="state"]').text(warehouse.state)
		$('#warehouseModal td[data-item-field="zip_code"]').text(warehouse.zip_code)
		$('#warehouseModal td[data-item-field="country"]').text(warehouse.country)

		$('#warehouseModal table#warehouseWorkers tbody').html('')
		$('#warehouseModal table#warehouseSupervisors tbody').html('')

		warehouse.workers.forEach(worker => {
			let elem = '<tr>'
				+ `<td width="60">${worker.name}</td>`
				+ '<td width="40">'
				<?php if($logged_user->role == 'admin') { ?>
				+ `<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeUser(${worker.id})"><?= langSlashes('Main.warehouses.remove') ?></button>`
				<?php }else{ ?>
				+ `<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeUser(${worker.id})" disabled><?= langSlashes('Main.warehouses.remove') ?></button>`
				<?php } ?>
				+ '</td>'
				+ '</tr>'
			
			$('#warehouseModal table#warehouseWorkers').append(elem)
		})

		warehouse.supervisors.forEach(supervisor => {
			let elem = '<tr>'
				+ `<td width="60">${supervisor.name}</td>`
				+ '<td width="40">'
				<?php if($logged_user->role == 'admin') { ?>
				+ `<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeUser(${supervisor.id})"><?= langSlashes('Main.warehouses.remove') ?></button>`
				<?php }else{ ?>
				+ `<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeUser(${supervisor.id})" disabled><?= langSlashes('Main.warehouses.remove') ?></button>`
				<?php } ?>
				+ '</td>'
				+ '</tr>'
			
			$('#warehouseModal table#warehouseSupervisors').append(elem)
		})

		loadPendingWorkers()
		loadPendingSupervisors()
	})
}

function loadPendingWorkers() {
	axios.get(`api/warehouses/${openWarehouse.id}/pending-workers`).then(response => {
		$('select#add_worker').empty()

		let elems = '<option value="" selected disabled><?= langSlashes('Main.warehouses.select_worker') ?></option>'

		response.data.forEach(worker => {
			elems += `<option value="${worker.id}">${worker.name}</option>`
		})

		$('select#add_worker').append(elems)
	})
}

function loadPendingSupervisors() {
	axios.get(`api/warehouses/${openWarehouse.id}/pending-supervisors`).then(response => {
		$('select#add_supervisor').empty()

		let elems = '<option value="" selected disabled><?= langSlashes('Main.warehouses.select_supervisor') ?></option>'

		response.data.forEach(supervisor => {
			elems += `<option value="${supervisor.id}">${supervisor.name}</option>`
		})

		$('select#add_supervisor').append(elems)
	})
}

function removeUser(userId) {
	showConfirmation('<?= langSlashes('Main.warehouses.remove_user_confirmation.title') ?>',
		'<?= langSlashes('Main.warehouses.remove_user_confirmation.msg') ?>',
		'<?= langSlashes('Main.warehouses.remove_user_confirmation.yes') ?>',
		'<?= langSlashes('Main.warehouses.remove_user_confirmation.no') ?>',
		() => {
			removeUserSubmit(userId)
			return true // True to close
		},
		() => {
			return true // True to close
		}
	)
}

function removeUserSubmit(userId) {
	axios.delete(`api/users/${userId}/remove-warehouse/${openWarehouse.id}`).then(response => {
		loadWarehouse(openWarehouse.id)
	})
}

function editWarehouse() {
	$('input[name=name]').val(openWarehouse.name)
	$('input[name=address]').val(openWarehouse.address)
	$('input[name=city]').val(openWarehouse.city)
	$('input[name=state]').val(openWarehouse.state)
	$('input[name=zip_code]').val(openWarehouse.zip_code)
	$('input[name=country]').val(openWarehouse.country)
	$('input[name=phone_number]').val(openWarehouse.phone_number)

	$('#editWarehouseModal').modal('show')
}

function editWarehouseSubmit() {
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

	if(!validator.validate())
		return

	axios.put(`api/warehouses/${openWarehouse.id}`, {
		name: $('input[name=name]').val(),
		address: $('input[name=address]').val(),
		city: $('input[name=city]').val(),
		state: $('input[name=state]').val(),
		zip_code: $('input[name=zip_code]').val(),
		country: $('input[name=country]').val(),
		phone_number: $('input[name=phone_number]').val()
	}).then(response => {
		$('#editWarehouseModal').modal('hide')
		table.ajax.reload()
	})
}

function deleteWarehouse() {
	showConfirmation('<?= langSlashes('Main.warehouses.delete_confirmation.title') ?>',
		'<?= langSlashes('Main.warehouses.delete_confirmation.msg') ?>',
		'<?= langSlashes('Main.warehouses.delete_confirmation.yes') ?>',
		'<?= langSlashes('Main.warehouses.delete_confirmation.no') ?>',
		() => {
			deleteWarehouseSubmit()
			return true // True to close
		},
		() => {
			return true // True to close
		}
	)
}

function deleteWarehouseSubmit() {
	axios.delete(`api/warehouses/${openWarehouse.id}`).then(response => {
		$('#warehouseModal').modal('hide')
		table.ajax.reload()
	})
}
</script>
<?= $this->endSection() ?>