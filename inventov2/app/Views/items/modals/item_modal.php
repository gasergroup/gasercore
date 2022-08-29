<!-- Start of item modal -->
<div class="modal fade" id="itemModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.items.item') ?></h5>

				<div>
					<button type="button" onclick="window.print()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.print') ?>
					</button>

					<?php if($logged_user->role == 'admin' || $logged_user->role == 'supervisor') { ?>
					<button type="button" onclick="addSupplierRelation()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.items.add_supplier') ?>
					</button>
					<?php } ?>

					<?php if($logged_user->role == 'admin') { ?>
					<button type="button" onclick="editItem()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.edit') ?>
					</button>
					<button type="button" onclick="deleteItem()" class="btn btn-outline-danger btn-sm">
						<?= lang('Main.misc.delete') ?>
					</button>
					<?php } ?>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.items.item_details') ?></strong>

						<div class="text-center mb-3" id="barcode">
							<svg></svg>
						</div>

						<div class="text-center mb-3" id="qr"></div>

						<table id="itemDetails" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.items.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.code') ?></th>
									<td width="60"width="60" data-item-field="code"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.barcode_type') ?></th>
									<td width="60" data-item-field="code_type"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.brand') ?></th>
									<td width="60" data-item-field="brand"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.category') ?></th>
									<td width="60" data-item-field="category"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.sale_price') ?></th>
									<td width="60" data-item-field="sale_price"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.sale_tax_percent') ?></th>
									<td width="60" data-item-field="sale_tax"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.weight_kg') ?></th>
									<td width="60" data-item-field="weight"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.dimensions_specific_m') ?></th>
									<td width="60" data-item-field="dimensions"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.alerts_specific') ?></th>
									<td width="60" data-item-field="min_max_alert"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.description') ?></th>
									<td width="60" data-item-field="description"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.items.notes') ?></th>
									<td width="60" data-item-field="notes"></td>
								</tr>
							</tbody>
						</table>

						<hr class="mb-4" />

						<strong class="d-block pb-2"><?= lang('Main.items.stock') ?></strong>
						<table id="itemStock" class="table stacked">
							<tbody></tbody>
						</table>
					</div>

					<div class="columns-separator"></div>

					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.items.suppliers.suppliers') ?></strong>

						<div id="itemSuppliers"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of item modal -->