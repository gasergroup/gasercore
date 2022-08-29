<!-- Start of purchase modal -->
<div class="modal fade" id="purchaseModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.purchases.purchase') ?></h5>

				<div>
					<button type="button" onclick="window.print()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.print') ?>
					</button>
					<button type="button" id="createReturn" onclick="createReturn()" class="btn btn-outline-primary btn-sm">
						<?= lang('Main.purchases.create_return') ?>
					</button>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.purchases.basic_information') ?></strong>
						<table id="purchaseInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.purchases.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.misc.created_at') ?></th>
									<td width="60" data-item-field="created_at"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.reference') ?></th>
									<td width="60" data-item-field="reference"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.warehouse_id') ?></th>
									<td width="60" data-item-field="warehouse_id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.warehouse_name') ?></th>
									<td width="60" data-item-field="warehouse_name"></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="columns-separator"></div>

					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.purchases.supplier.supplier') ?></strong>
						<table id="supplierInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.address') ?></th>
									<td width="60" data-item-field="address"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.city') ?></th>
									<td width="60" data-item-field="city"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.state') ?></th>
									<td width="60" data-item-field="state"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.zip_code') ?></th>
									<td width="60" data-item-field="zip_code"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.purchases.supplier.country') ?></th>
									<td width="60" data-item-field="country"></td>
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
										<th><?= lang('Main.purchases.items.item_name') ?></th>
										<th><?= lang('Main.purchases.items.unit_price') ?></th>
										<th><?= lang('Main.purchases.items.quantity') ?></th>
										<th><?= lang('Main.purchases.items.subtotal') ?></th>
										<th><?= lang('Main.purchases.items.tax') ?></th>
										<th><?= lang('Main.purchases.items.total') ?></th>
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
							<strong><?= lang('Main.purchases.notes') ?></strong>
							<div id="notes"></div>
						</div>
					</div>

					<div class="mt-3 col-sm-2">
						
					</div>

					<div class="col-sm-4 text-break pl-2 pr-2 mt-3">
						<table id="summary" class="table stacked">
							<tbody>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.purchases.subtotal') ?></th>
									<td width="60" data-summary-field="subtotal"><?= $settings->currency_symbol ?> 0</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.purchases.discount') ?></th>
									<td width="60" data-summary-field="discount"><?= $settings->currency_symbol ?> 0</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.purchases.shipping_cost') ?></th>
									<td width="60" data-summary-field="shipping"><?= $settings->currency_symbol ?> 0</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.purchases.tax') ?></th>
									<td width="60" data-summary-field="tax">0%</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-bold"><?= lang('Main.purchases.grand_total') ?></th>
									<td width="60" data-summary-field="total" class="font-weight-bold"><?= $settings->currency_symbol ?> 0</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of purchase modal -->