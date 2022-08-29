<!-- Start of edit item modal -->
<div class="modal fade" id="editWarehouseModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.warehouses.edit_warehouse') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.warehouses.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.warehouses.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.warehouses.physical_information') ?>
							</h6>

							<div class="form-group">
								<label for="address" class="d-block"><?= lang('Main.warehouses.address') ?></label>
								<input type="text" name="address" id="address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="city" class="d-block"><?= lang('Main.warehouses.city') ?></label>
										<input type="text" id="city" name="city" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="state" class="d-block"><?= lang('Main.warehouses.state') ?></label>
										<input type="text" id="state" name="state" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="zip_code" class="d-block"><?= lang('Main.warehouses.zip_code') ?></label>
										<input type="text" id="zip_code" name="zip_code" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="country" class="d-block"><?= lang('Main.warehouses.country') ?></label>
										<input type="text" id="country" name="country" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.warehouses.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.warehouses.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of edit item modal -->