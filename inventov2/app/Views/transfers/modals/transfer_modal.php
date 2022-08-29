<!-- Start of transfer modal -->
<div class="modal fade" id="transferModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.transfers.transfer') ?></h5>

				<div>
					<button type="button" onclick="window.print()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.print') ?>
					</button>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.transfers.basic_information') ?></strong>
						<table id="transferInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.transfers.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.transfers.from_warehouse_id') ?></th>
									<td width="60" data-item-field="from_warehouse_id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.transfers.from_warehouse_name') ?></th>
									<td width="60" data-item-field="from_warehouse_name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.transfers.to_warehouse_id') ?></th>
									<td width="60" data-item-field="to_warehouse_id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.transfers.to_warehouse_name') ?></th>
									<td width="60" data-item-field="to_warehouse_name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.transfers.created_by') ?></th>
									<td width="60" data-item-field="created_by"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.transfers.created_at') ?></th>
									<td width="60" data-item-field="created_at"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="row mt-4">
					<div class="col-sm text-break pl-2 pr-2">
						<div class="table-responsive">
							<table id="items" class="table table-bordered">
								<thead>
									<tr>
										<th><?= lang('Main.transfers.items.item_name') ?></th>
										<th><?= lang('Main.transfers.items.transfer_quantity') ?></th>
										<th><?= lang('Main.transfers.items.source_warehouse_change') ?></th>
										<th><?= lang('Main.transfers.items.target_warehouse_change') ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="row mt-n3">
					<div class="col-sm-6 text-break pl-2 pr-2 mt-3">
						<div class="form-group">
							<strong><?= lang('Main.transfers.notes') ?></strong>
							<div id="notes"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of transfer modal -->