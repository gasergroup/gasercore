<!-- Start of edit supplier modal -->
<div class="modal fade" id="editSupplierModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.suppliers.edit_supplier') ?></h5>
			</header>

			<div class="modal-body">
			<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.suppliers.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.suppliers.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="internal_name" class="d-block"><?= lang('Main.suppliers.internal_name') ?></label>
								<input type="text" name="internal_name" id="internal_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="company_name" class="d-block"><?= lang('Main.suppliers.company_name') ?></label>
								<input type="text" name="company_name" id="company_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="vat" class="d-block"><?= lang('Main.suppliers.vat') ?></label>
								<input type="text" name="vat" id="vat" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="email_address" class="d-block"><?= lang('Main.suppliers.email_address') ?></label>
								<input type="text" name="email_address" id="email_address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.suppliers.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<hr class="mt-4 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.suppliers.physical_information') ?>
							</h6>

							<div class="form-group">
								<label for="address" class="d-block"><?= lang('Main.suppliers.address') ?></label>
								<input type="text" name="address" id="address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="city" class="d-block"><?= lang('Main.suppliers.city') ?></label>
										<input type="text" id="city" name="city" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="state" class="d-block"><?= lang('Main.suppliers.state') ?></label>
										<input type="text" id="state" name="state" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="zip_code" class="d-block"><?= lang('Main.suppliers.zip_code') ?></label>
										<input type="text" id="zip_code" name="zip_code" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="country" class="d-block"><?= lang('Main.suppliers.country') ?></label>
										<input type="text" id="country" name="country" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.suppliers.custom_information') ?>
							</h6>

							<div class="form-group">
								<label for="custom_field_1" class="d-block"><?= lang('Main.suppliers.custom_field_1') ?></label>
								<input type="text" id="custom_field_1" name="custom_field_1" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="custom_field_2" class="d-block"><?= lang('Main.suppliers.custom_field_2') ?></label>
								<input type="text" id="custom_field_2" name="custom_field_2" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="custom_field_3" class="d-block"><?= lang('Main.suppliers.custom_field_3') ?></label>
								<input type="text" id="custom_field_3" name="custom_field_3" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<hr class="mt-4 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.suppliers.notes') ?>
							</h6>

							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.suppliers.notes') ?></label>
								<textarea id="notes" name="notes" rows="5" wrap="soft" class="form-control"></textarea>
							</div>

						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.suppliers.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of edit supplier modal -->