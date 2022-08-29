<!-- Start of item modal -->
<div class="modal fade" id="warehouseModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.warehouses.warehouse') ?></h5>

				<div>
					<?php if($logged_user->role == 'admin') { ?>
					<button type="button" onclick="editWarehouse()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.edit') ?>
					</button>
					<button type="button" onclick="deleteWarehouse()" class="btn btn-outline-danger btn-sm">
						<?= lang('Main.misc.delete') ?>
					</button>
					<?php } ?>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.warehouses.basic_information') ?></strong>
						<table id="warehouseBasicInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.warehouses.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.warehouses.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.warehouses.phone_number') ?></th>
									<td width="60" data-item-field="phone_number"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.misc.created_by') ?></th>
									<td width="60" data-item-field="created_by"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.misc.created_at') ?></th>
									<td width="60" data-item-field="created_at"></td>
								</tr>
							</tbody>
						</table>

						<hr class="mb-4" />

						<strong class="d-block pb-2"><?= lang('Main.warehouses.physical_information') ?></strong>
						<table id="warehousePhysicalInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.warehouses.address') ?></th>
									<td width="60" data-item-field="address"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.warehouses.city') ?></th>
									<td width="60" data-item-field="city"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.warehouses.state') ?></th>
									<td width="60" data-item-field="state"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.warehouses.zip_code') ?></th>
									<td width="60" data-item-field="zip_code"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.warehouses.country') ?></th>
									<td width="60" data-item-field="country"></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="columns-separator"></div>

					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-0"><?= lang('Main.warehouses.workers') ?></strong>

						<?php if($logged_user->role == 'admin') { ?>
						<span class="autocomplete-desc pt-1 pb-2">
							<?= lang('Main.warehouses.workers_help') ?>
						</span>
						<select name="add_worker" id="add_worker" class="custom-select mb-3">
							<option value="" selected disabled><?= lang('Main.warehouses.select_worker') ?></option>
						</select>
						<?php } ?>

						<table id="warehouseWorkers" class="table">
							<thead>
								<tr>
									<th><?= lang('Main.warehouses.worker_name') ?></th>
									<th><?= lang('Main.warehouses.action') ?></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>

						<strong class="d-block pb-0 mt-4"><?= lang('Main.warehouses.supervisors') ?>e</strong>

						<?php if($logged_user->role == 'admin') { ?>
						<span class="autocomplete-desc pt-1 pb-2">
							<?= lang('Main.warehouses.supervisors_help') ?>
						</span>
						<select name="add_supervisor" id="add_supervisor" class="custom-select mb-3">
							<option value="" selected disabled><?= lang('Main.warehouses.select_supervisor') ?></option>
						</select>
						<?php } ?>

						<table id="warehouseSupervisors" class="table">
							<thead>
								<tr>
									<th><?= lang('Main.warehouses.supervisor_name') ?></th>
									<th><?= lang('Main.warehouses.action') ?></th>
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
<!-- End of item modal -->