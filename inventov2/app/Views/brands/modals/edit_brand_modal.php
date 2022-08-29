<!-- Start of edit brand modal -->
<div class="modal fade" id="editBrandModal">
	<div class="modal-dialog modal-xl modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.brands.edit_brand') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<h6 class="h6-5 text-secondary mb-3">
						<?= lang('Main.brands.basic_information') ?>
					</h6>

					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.brands.brand_name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>

						<!-- Separator -->
						<div class="columns-separator"></div>

						<!-- Right -->
						<div class="col-sm text-break pl-2 pr-2">
							<div class="form-group">
								<label for="description" class="d-block"><?= lang('Main.brands.description') ?></label>
								<textarea id="description" name="description" rows="5" class="form-control"></textarea>
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.brands.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of edit brand modal -->