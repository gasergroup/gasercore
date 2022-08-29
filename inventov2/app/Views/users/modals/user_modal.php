<!-- Start of user modal -->
<div class="modal fade" id="userModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.users.user') ?></h5>

				<div>
					<button type="button" onclick="editUser()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.edit') ?>
					</button>
					<button type="button" id="buttonDeleteUser" onclick="deleteUser()" class="btn btn-outline-danger btn-sm">
						<?= lang('Main.misc.delete') ?>
					</button>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.users.basic_information') ?></strong>
						<table id="warehouseBasicInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.users.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.users.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.users.username') ?></th>
									<td width="60" data-item-field="username"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.users.email_address') ?></th>
									<td width="60" data-item-field="email_address"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.users.phone_number') ?></th>
									<td width="60" data-item-field="phone_number"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.users.role') ?></th>
									<td width="60" data-item-field="role"></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="columns-separator"></div>

					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-0"><?= lang('Main.users.warehouses') ?></strong>

						<div id="accessEntireSystem">
							<?= lang('Main.users.access_all_system') ?>
						</div>

						<div id="accessWarehouses">
							<span class="autocomplete-desc pt-1 pb-2">
								<?= lang('Main.users.warehouses_help') ?>
							</span>
							<select name="add_warehouse" id="add_warehouse" class="custom-select mb-3">
								<option value="" selected disabled><?= lang('Main.users.select_warehouse') ?></option>
							</select>
							<table id="userWarehouses" class="table">
								<thead>
									<tr>
										<th><?= lang('Main.users.warehouse') ?></th>
										<th><?= lang('Main.users.action') ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of user modal -->