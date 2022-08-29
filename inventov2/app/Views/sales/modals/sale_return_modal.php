<!-- Start of sale modal -->
<div class="modal fade" id="returnModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.sales_returns.sale_return') ?></h5>

				<div>
					<button type="button" onclick="window.print()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.print') ?>
					</button>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.sales_returns.basic_information') ?></strong>
						<table id="returnInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.misc.created_at') ?></th>
									<td width="60" data-item-field="created_at"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.reference') ?></th>
									<td width="60" data-item-field="reference"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.sale_reference') ?></th>
									<td width="60" data-item-field="sale_reference"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.warehouse') ?></th>
									<td width="60" data-item-field="warehouse"></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="columns-separator"></div>

					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.sales_returns.customer.customer') ?></strong>
						<table id="customerInformation" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.customer.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.customer.address') ?></th>
									<td width="60" data-item-field="address"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.customer.city') ?></th>
									<td width="60" data-item-field="city"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.customer.state') ?></th>
									<td width="60" data-item-field="state"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.customer.zip_code') ?></th>
									<td width="60" data-item-field="zip_code"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.sales_returns.customer.country') ?></th>
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
										<th><?= lang('Main.sales_returns.items.item_name') ?></th>
										<th><?= lang('Main.sales_returns.items.unit_price') ?></th>
										<th><?= lang('Main.sales_returns.items.quantity') ?></th>
										<th><?= lang('Main.sales_returns.items.subtotal') ?></th>
										<th><?= lang('Main.sales_returns.items.tax') ?></th>
										<th><?= lang('Main.sales_returns.items.total') ?></th>
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
							<strong><?= lang('Main.sales_returns.notes') ?></strong>
							<div id="notes"></div>
						</div>
					</div>

					<div class="mt-3 col-sm-2">
						
					</div>

					<div class="col-sm-4 text-break pl-2 pr-2 mt-3">
						<table id="summary" class="table stacked">
							<tbody>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.sales_returns.subtotal') ?></th>
									<td width="60" data-summary-field="subtotal">$0</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.sales_returns.discount') ?></th>
									<td width="60" data-summary-field="discount">$0</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.sales_returns.shipping_cost') ?></th>
									<td width="60" data-summary-field="shipping">$0</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-normal"><?= lang('Main.sales_returns.tax') ?></th>
									<td width="60" data-summary-field="tax">0%</td>
								</tr>
								<tr>
									<th width="40" class="font-weight-bold"><?= lang('Main.sales_returns.total_refunded') ?></th>
									<td width="60" data-summary-field="total" class="font-weight-bold">$0</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of sale modal -->