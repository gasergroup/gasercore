<!-- Start of supplier modal -->
<div class="modal fade" id="supplierModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.suppliers.supplier') ?></h5>

				<div>
					<?php if($logged_user->role == 'admin' || $logged_user->role == 'supervisor') { ?>
					<button type="button" onclick="editSupplier()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.edit') ?>
					</button>
					<?php } ?>

					<?php if($logged_user->role == 'admin') { ?>
					<button type="button" onclick="deleteSupplier()" class="btn btn-outline-danger btn-sm">
						<?= lang('Main.misc.delete') ?>
					</button>
					<?php } ?>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.suppliers.supplier_details') ?></strong>

						<table id="supplierDetails" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.suppliers.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.internal_name') ?></th>
									<td data-item-field="internal_name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.company_name') ?></th>
									<td data-item-field="company_name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.vat') ?></th>
									<td width="60" data-item-field="vat"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.email_address') ?></th>
									<td width="60" data-item-field="email_address"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.phone_number') ?></th>
									<td width="60" data-item-field="phone_number"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.address') ?></th>
									<td width="60" data-item-field="address"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.city') ?></th>
									<td width="60" data-item-field="city"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.country') ?></th>
									<td width="60" data-item-field="country"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.state') ?></th>
									<td width="60" data-item-field="state"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.zip_code') ?></th>
									<td width="60" data-item-field="zip_code"></td>
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
					</div>

					<div class="columns-separator"></div>

					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.suppliers.custom_information') ?></strong>
						<table id="customFields" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.suppliers.custom_field_1') ?></th>
									<td width="60" data-item-field="custom_field_1"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.custom_field_2') ?></th>
									<td width="60" data-item-field="custom_field_2"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.suppliers.custom_field_3') ?></th>
									<td width="60" data-item-field="custom_field_3"></td>
								</tr>
							</tbody>
						</table>

						<hr class="mt-4 mb-4" />

						<strong class="d-block pb-2"><?= lang('Main.suppliers.notes') ?></strong>
						<table id="notes" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.suppliers.notes') ?></th>
									<td width="60" data-item-field="notes"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of supplier modal -->