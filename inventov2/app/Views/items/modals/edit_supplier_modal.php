<!-- Start of add supplier modal -->
<div class="modal fade" id="editSupplierModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.items.suppliers.edit') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="edit_supplier" class="d-block"><?= lang('Main.items.suppliers.supplier') ?>*</label>
								<select name="edit_supplier" id="edit_supplier" class="custom-select" disabled></select>
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="edit_price" class="d-block"><?= lang('Main.items.suppliers.price') ?>*</label>
								<input type="text" id="edit_price" name="edit_price" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="edit_part_number" class="d-block"><?= lang('Main.items.suppliers.part_number') ?></label>
								<input type="text" id="edit_part_number" name="edit_part_number" class="form-control" />
								<div class="invalid-feedback"></div>
								<small class="form-text text-muted">
									<?= lang('Main.items.suppliers.part_number_help') ?>
								</small>
							</div>

							<div class="form-group">
								<label for="edit_tax" class="d-block"><?= lang('Main.items.suppliers.tax') ?>*</label>
								<input type="text" id="edit_tax" name="edit_tax" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.items.suppliers.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of add supplier modal -->