<?= $this->extend('templates/master') ?>

<?= $this->section('content') ?>
<?= $this->include('users/modals/user_modal') ?>
<?= $this->include('users/modals/edit_user_modal') ?>
<?= $this->include('components/error_modal') ?>
<?= $this->include('components/confirmation_modal') ?>

<!-- Start of Users -->
<div class="row">
	<div class="px-2 py-1 col">
		<div class="section variant-2">
			<div class="header d-flex align-items-center justify-content-between">
				<div class="title">
					<?= lang('Main.users.users') ?>
				</div>

				<div class="buttons d-flex">
					<?php if($logged_user->role == 'admin') { ?>
					<a href="<?= base_url('api/users/export') ?>" class="btn px-3 btn-outline-primary btn-sm mr-2">
						<?= lang('Main.misc.export_csv') ?>
					</a>
					<?php } ?>

					<a href="<?= base_url('users/new') ?>" class="btn px-3 btn-outline-primary btn-sm">
						<?= lang('Main.users.new_user') ?>
					</a>
				</div>
			</div>

			<div class="content">
				<div class="table-responsive">
					<table id="users" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('Main.users.name') ?></th>
								<th><?= lang('Main.users.username') ?></th>
								<th><?= lang('Main.users.email_address') ?></th>
								<th><?= lang('Main.users.phone_number') ?></th>
								<th><?= lang('Main.users.role') ?></th>
								<th><?= lang('Main.users.access_to_warehouses') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of Users -->
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script type="text/javascript">
'use strict';
	
var openUser = {};
var table = {};

(function($) {
	'use strict';

	$('document').ready(function() {
		$('.main-loader').fadeOut(100)

		// Link table to the loader
		$('table#users').on('processing.dt', (e, settings, processing) => {
			if(processing)
				$('.main-loader').fadeIn(100)
			else
				$('.main-loader').fadeOut(100)
		})

		// Load table
		table = $('table#users').DataTable({
			serverSide: true,
			ajax: "<?= base_url('api/users') ?>",
			columns: [
				{ data: "name" },
				{ data: "username" },
				{ data: "email_address" },
				{ data: "phone_number" },
				{ data: "role" },
				{
					data: 'warehouses',
					render: (data, type, row) => {
						if(row.role == 'admin')
							return "<?= langSlashes('Main.users.all') ?>"

						return data
					}
				}
			]
		})

		$('table#users tbody').on('click', 'tr', function() {
			let id = table.row(this).data().DT_RowId
			loadUser(id)
		})

		$('#userModal').on('hide.bs.modal', e => {
			window.history.pushState(null, '', `<?= base_url() ?>/users`)
		})

		$('#editUserModal').on('hide.bs.modal', e => {
			loadUser(openUser.id)
		})

		$('#editUserModal').on('show.bs.modal', e => {
			$('#userModal').modal('hide')
		})

		<?php if($userId != false) { ?>
		loadUser(<?= $userId ?>)
		<?php } ?>

		// When selecting a warehouse to give a user access
		$('select[name=add_warehouse]').on('change', e => {
			let newWarehouseId = $(e.currentTarget).val()

			axios.put(`api/users/${openUser.id}/add-warehouse/${newWarehouseId}`).then(response => {
				loadUser(openUser.id)
			})
		})

		$('#editUserModal form').on('submit', e => {
			e.preventDefault()
			editUserSubmit()
		})
	})
})(jQuery)

function loadUser(id) {
	axios.get(`api/users/${id}`).then(response => {
		let user = response.data

		openUser = user

		window.history.pushState(null, '', `<?= base_url() ?>/users/${id}`)

		$('#userModal').modal('show')

		$('#userModal td[data-item-field="id"]').text(user.id)
		$('#userModal td[data-item-field="name"]').text(user.name)
		$('#userModal td[data-item-field="username"]').text(user.username)
		$('#userModal td[data-item-field="email_address"]').text(user.email_address)
		$('#userModal td[data-item-field="phone_number"]').text(user.phone_number)
		$('#userModal td[data-item-field="role"]').text(user.role)

		$('#userModal table#userWarehouses tbody').html('')

		let currentUser = <?= $logged_user->id ?>;
		if(currentUser == id)
			$('#userModal button#buttonDeleteUser').attr('disabled', true)
		else
			$('#userModal button#buttonDeleteUser').attr('disabled', false)

		if(user.role != 'worker' && user.role != 'supervisor') {
			$('#userModal #accessEntireSystem').css('display', 'block')
			$('#userModal #accessWarehouses').css('display', 'none');
		}else{
			$('#userModal #accessEntireSystem').css('display', 'none')
			$('#userModal #accessWarehouses').css('display', 'block');

			user.warehouses.forEach(warehouse => {
				let elem = '<tr>'
				+ `<td width="60">${warehouse.name}</td>`
				+ '<td width="40">'
				+ `<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeWarehouse(${warehouse.id})"><?= langSlashes('Main.users.remove') ?></button>`
				+ '</td>'
				+ '</tr>'
			
				$('#userModal table#userWarehouses').append(elem)
			})
		}

		if(user.role == 'worker' || user.role == 'supervisor')
			loadPendingWarehouses()
	})
}

function loadPendingWarehouses() {
	axios.get(`api/users/${openUser.id}/pending-warehouses`).then(response => {
		$('select#add_warehouse').empty()

		let elems = '<option value="" selected disabled><?= langSlashes('Main.users.select_warehouse') ?></option>'

		response.data.forEach(warehouse => {
			elems += `<option value="${warehouse.id}">${warehouse.name}</option>`
		})

		$('select#add_warehouse').append(elems)
	})
}

function removeWarehouse(warehouseId) {
	showConfirmation('<?= langSlashes('Main.users.remove_warehouse_confirmation.title') ?>',
		'<?= langSlashes('Main.users.remove_warehouse_confirmation.msg') ?>',
		'<?= langSlashes('Main.users.remove_warehouse_confirmation.yes') ?>',
		'<?= langSlashes('Main.users.remove_warehouse_confirmation.no') ?>',
		() => {
			removeWarehouseSubmit(warehouseId)
			return true // True to close
		},
		() => {
			return true // True to close
		}
	)
}

function removeWarehouseSubmit(warehouseId) {
	axios.delete(`api/users/${openUser.id}/remove-warehouse/${warehouseId}`).then(response => {
		loadUser(openUser.id)
	})
}


function editUser() {
	$('input[name="username"]').val(openUser.username)
	$('input[name="name"]').val(openUser.name)
	$('input[name="email_address"]').val(openUser.email_address)
	$('input[name="phone_number"]').val(openUser.phone_number)
	$('input[name="password"]').val('')
	$('input[name="password_confirmation"]').val('')

	$('#editUserModal').modal('show')
}

function editUserSubmit() {
	let validator = new Validator()
	validator.addInputTextVal('username', 'minLength', 5, "<?= langSlashes('Validation.users.username_min_length') ?>")
	validator.addInputTextVal('username', 'maxLength', 30, "<?= langSlashes('Validation.users.username_max_length') ?>")
	validator.addInputTextVal('name', 'minLength', 2, "<?= langSlashes('Validation.users.name_min_length') ?>")
	validator.addInputTextVal('name', 'maxLength', 100, "<?= langSlashes('Validation.users.name_max_length') ?>")
	validator.addInputText('email_address', 'email-address', "<?= langSlashes('Validation.users.email_address_invalid') ?>")
	validator.addInputTextVal('phone_number', 'maxLength', 20, "<?= langSlashes('Validation.users.phone_number_max_length') ?>")
	validator.addInputTextCustom('password', value => {
		if(value != '' && value.length < 5)
			return false
		return true
	}, "<?= langSlashes('Validation.users.password_min_length') ?>")
	validator.addInputTextCustom('password', value => {
		if(value != '' && value.length > 30)
			return false
		return true
	}, "<?= langSlashes('Validation.users.password_max_length') ?>")
	validator.addInputTextCustom('password_confirmation', value => {
		let password = $('input[name=password]').val()

		return value === password
	}, "<?= langSlashes('Validation.users.password_missmatch') ?>")

	if(!validator.validate())
		return

	let data = {
		username: $('input[name="username"]').val(),
		name: $('input[name="name"]').val(),
		email_address: $('input[name="email_address"]').val(),
		phone_number: $('input[name="phone_number"]').val()
	}

	let password = $('input[name=password]').val()

	if(password != '')
		data.password = password

	axios.put(`api/users/${openUser.id}`, data).then(response => {
		$('#editUserModal').modal('hide')
		table.ajax.reload()
	})
}

function deleteUser() {
	showConfirmation('<?= langSlashes('Main.users.delete_confirmation.title') ?>',
		'<?= langSlashes('Main.users.delete_confirmation.msg') ?>',
		'<?= langSlashes('Main.users.delete_confirmation.yes') ?>',
		'<?= langSlashes('Main.users.delete_confirmation.no') ?>',
		() => {
			deleteUserSubmit()
			return true // True to close
		},
		() => {
			return true // True to close
		}
	)
}

function deleteUserSubmit() {
	axios.delete(`api/users/${openUser.id}`).then(response => {
		$('#userModal').modal('hide')
		table.ajax.reload()
	})
}
</script>
<?= $this->endSection() ?>