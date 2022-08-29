<!-- Start of edit item modal -->
<div class="modal fade" id="editItemModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.items.edit_item') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="edit_item_name" class="d-block"><?= lang('Main.items.item_name') ?>*</label>
								<input type="text" name="edit_item_name" id="edit_item_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="edit_item_code_type" class="d-block"><?= lang('Main.items.barcode_type') ?></label>
								<select name="edit_item_code_type" id="edit_item_code_type" class="custom-select">
									<option value="none"><?= lang('Main.items.none') ?></option>
									<option value="code39"><?= lang('Main.items.code39') ?></option>
									<option value="code128"><?= lang('Main.items.code128') ?></option>
									<option value="ean-8"><?= lang('Main.items.ean8') ?></option>
									<option value="ean-13"><?= lang('Main.items.ean13') ?></option>
									<option value="upc-a"><?= lang('Main.items.upca') ?></option>
									<option value="qr"><?= lang('Main.items.qr') ?></option>
								</select>

								<small class="form-text text-muted">
									<?= lang('Main.items.barcode_type_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="edit_item_code" class="d-block"><?= lang('Main.items.code') ?>*</label>
								<div class="input-group">
									<input type="text" id="edit_item_code" name="edit_item_code" class="form-control" />
									<div class="input-group-append">
										<button type="button" onclick="generateCode()" class="btn btn-primary">
											<i class="fas fa-sync-alt"></i>
										</button>
									</div>
									<div class="invalid-feedback"></div>
								</div>
								
								<small class="form-text text-muted">
									<?= lang('Main.items.code_help.none') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="edit_item_brand" class="d-block"><?= lang('Main.items.brand') ?></label>
								<select name="edit_item_brand" id="edit_item_brand" class="custom-select">
									<option value=""><?= lang('Main.items.none') ?></option>
									<?php foreach($brands as $brand) { ?>
									<option value="<?= $brand->id ?>"><?= $brand->name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="form-group">
								<label for="edit_item_category" class="d-block"><?= lang('Main.items.category') ?></label>
								<select name="edit_item_category" id="edit_item_category" class="custom-select">
									<option value=""><?= lang('Main.items.none') ?></option>
									<?php foreach($categories as $category) { ?>
									<option value="<?= $category->id ?>"><?= $category->name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_sale_price" class="d-block"><?= lang('Main.items.sale_price') ?>*</label>
										<input type="text" id="edit_item_sale_price" name="edit_item_sale_price" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_sale_tax" class="d-block"><?= lang('Main.items.sale_tax_percent') ?></label>
										<input type="text" id="edit_item_sale_tax" name="edit_item_sale_tax" class="form-control" value="0" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="edit_item_description" class="d-block"><?= lang('Main.items.description') ?></label>
								<textarea id="description" name="edit_item_description" rows="5" wrap="soft" class="form-control"></textarea>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.dimensions') ?>
							</h6>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_weight" class="d-block"><?= lang('Main.items.weight_kg') ?></label>
										<input type="text" id="edit_item_weight" name="edit_item_weight" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_width" class="d-block"><?= lang('Main.items.width_m') ?></label>
										<input type="text" id="edit_item_width" name="edit_item_width" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_height" class="d-block"><?= lang('Main.items.height_m') ?></label>
										<input type="text" id="edit_item_height" name="edit_item_height" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_depth" class="d-block"><?= lang('Main.items.depth_m') ?></label>
										<input type="text" id="edit_item_depth" name="edit_item_depth" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<hr class="mt-3 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.alerts') ?>
							</h6>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_min_alert" class="d-block"><?= lang('Main.items.minimum_qty_alert') ?></label>
										<input type="text" id="edit_item_min_alert" name="edit_item_min_alert" class="form-control" />
										<div class="invalid-feedback"></div>
										<small class="form-text text-muted">
											<?= lang('Main.items.minimum_qty_alert_help') ?>
										</small>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="edit_item_max_alert" class="d-block"><?= lang('Main.items.maximum_qty_alert') ?></label>
										<input type="text" id="edit_item_max_alert" name="edit_item_max_alert" class="form-control" />
										<div class="invalid-feedback"></div>
										<small class="form-text text-muted">
											<?= lang('Main.items.maximum_qty_alert_help') ?>
										</small>
									</div>
								</div>
							</div>

							<hr class="mt-3 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.items.notes') ?>
							</h6>

							<div class="form-group">
								<label for="edit_item_notes" class="d-block"><?= lang('Main.items.notes') ?></label>
								<textarea id="notes" name="edit_item_notes" rows="5" wrap="soft" class="form-control"></textarea>
							</div>

						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.items.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of edit item modal -->