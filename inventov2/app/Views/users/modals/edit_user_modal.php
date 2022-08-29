<!-- Start of edit user modal -->
<div class="modal fade" id="editUserModal">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.users.edit_user') ?></h5>
			</header>

			<div class="modal-body">
				<form>
					<div class="row mt-0">
						<!-- Left -->
						<div class="col-sm text-break pl-2 pr-2">
							<h6 class="h6-5 text-secondary mb-3">
								<?= lang('Main.users.account_information') ?>
							</h6>

							<div class="form-group">
								<label for="username" class="d-block"><?= lang('Main.users.username') ?>*</label>
								<input type="text" name="username" id="username" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="name" class="d-block"><?= lang('Main.users.name') ?>*</label>
								<input type="text" name="name" id="name" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="email_address" class="d-block"><?= lang('Main.users.email_address') ?>*</label>
								<input type="text" name="email_address" id="email_address" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="phone_number" class="d-block"><?= lang('Main.users.phone_number') ?></label>
								<input type="text" name="phone_number" id="phone_number" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<h6 class="h6-5 text-secondary mt-5 mb-2">
								<?= lang('Main.users.change_password') ?>
							</h6>
							<span class="autocomplete-desc mb-2">
								<?= lang('Main.users.change_password_help') ?>
							</span>

							<div class="form-group">
								<label for="password" class="d-block"><?= lang('Main.users.password') ?></label>
								<input type="password" name="password" id="password" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>

							<div class="form-group">
								<label for="password_confirmation" class="d-block"><?= lang('Main.users.password_confirmation') ?></label>
								<input type="password" name="password_confirmation" id="password_confirmation" class="form-control" />
								<div class="invalid-feedback"></div>
							</div>
						</div>
					</div>

					<hr class="mt-4" />

					<div class="text-right mt-2 mb-2">
						<button type="submit" class="btn px-3 btn-outline-primary btn-sm">
							<?= lang('Main.users.save') ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of edit user modal -->