<!-- Start of edit customer modal -->
<div class="modal fade" id="editCustomerModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.customers.edit_customer') ?></h5>
			</header>

			<div class="modal-body">
			<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.basic_information') ?>
							</h6>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.customers.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="internal_name" class="d-block"><?= lang('Main.customers.internal_name') ?></label>
								<input type="text" name="internal_name" id="internal_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="company_name" class="d-block"><?= lang('Main.customers.company_name') ?></label>
								<input type="text" name="company_name" id="company_name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="tax_number" class="d-block"><?= lang('Main.customers.tax_number') ?></label>
								<input type="text" name="tax_number" id="tax_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="email_address" class="d-block"><?= lang('Main.customers.email_address') ?></label>
								<input type="text" name="email_address" id="email_address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.customers.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<hr class="mt-4 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.physical_information') ?>
							</h6>

							<div class="form-group">
								<label for="address" class="d-block"><?= lang('Main.customers.address') ?></label>
								<input type="text" name="address" id="address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="city" class="d-block"><?= lang('Main.customers.city') ?></label>
										<input type="text" id="city" name="city" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="state" class="d-block"><?= lang('Main.customers.state') ?></label>
										<input type="text" id="state" name="state" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-sm">
									<div class="form-group">
										<label for="zip_code" class="d-block"><?= lang('Main.customers.zip_code') ?></label>
										<input type="text" id="zip_code" name="zip_code" class="form-control" />
										<div class="invalid-feedback"></div>
									</div>
								</div>

								<div class="col-sm">
									<div class="form-group">
										<label for="country" class="d-block"><?= lang('Main.customers.country') ?></label>
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
								<?= lang('Main.customers.custom_information') ?>
							</h6>

							<div class="form-group">
								<label for="custom_field_1" class="d-block"><?= lang('Main.customers.custom_field_1') ?></label>
								<input type="text" id="custom_field_1" name="custom_field_1" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="custom_field_2" class="d-block"><?= lang('Main.customers.custom_field_2') ?></label>
								<input type="text" id="custom_field_2" name="custom_field_2" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="custom_field_3" class="d-block"><?= lang('Main.customers.custom_field_3') ?></label>
								<input type="text" id="custom_field_3" name="custom_field_3" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<hr class="mt-4 mb-4" />

							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.customers.notes') ?>
							</h6>

							<div class="form-group">
								<label for="notes" class="d-block"><?= lang('Main.customers.notes') ?></label>
								<textarea id="notes" name="notes" rows="5" wrap="soft" class="form-control"></textarea>
							</div>

						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.customers.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of edit customer modal -->