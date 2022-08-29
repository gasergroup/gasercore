<!-- Start of add supplier modal -->
<div class="modal fade" id="addSupplierModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.items.suppliers.add_supplier_to_item') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="add_supplier" class="d-block"><?= lang('Main.items.suppliers.supplier') ?>*</label>
								<select name="add_supplier" id="add_supplier" class="custom-select">
									<option value=""><?= lang('Main.items.none') ?></option>
									<?php foreach($suppliers as $supplier) { ?>
									<option value="<?= $supplier->id ?>"><?= $supplier->name ?></option>
									<?php } ?>
								</select>
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="add_price" class="d-block"><?= lang('Main.items.suppliers.price') ?>*</label>
								<input type="text" id="add_price" name="add_price" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="add_part_number" class="d-block"><?= lang('Main.items.suppliers.part_number') ?></label>
								<input type="text" id="add_part_number" name="add_part_number" class="form-control" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.items.suppliers.part_number_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="add_tax" class="d-block"><?= lang('Main.items.suppliers.tax') ?>*</label>
								<input type="text" id="add_tax" name="add_tax" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.items.suppliers.add') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of add supplier modal -->