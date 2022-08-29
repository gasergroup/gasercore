<!-- Start of brand modal -->
<div class="modal fade" id="brandModal">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<header class="modal-header">
				<h5><?= lang('Main.brands.brand') ?></h5>

				<div>
					<?php if($logged_user->role == 'admin' || $logged_user->role == 'supervisor') { ?>
					<button type="button" onclick="editBrand()" class="btn mr-2 btn-outline-primary btn-sm">
						<?= lang('Main.misc.edit') ?>
					</button>
					<?php } ?>

					<?php if($logged_user->role == 'admin') { ?>
					<button type="button" onclick="deleteBrand()" class="btn btn-outline-danger btn-sm">
						<?= lang('Main.misc.delete') ?>
					</button>
					<?php } ?>
				</div>
			</header>

			<div class="modal-body">
				<div class="row mt-0">
					<div class="col-sm text-break pl-2 pr-2">
						<strong class="d-block pb-2"><?= lang('Main.brands.brand_details') ?></strong>

						<table id="brandDetails" class="table stacked">
							<tbody>
								<tr>
									<th width="40"><?= lang('Main.brands.id') ?></th>
									<td width="60" data-item-field="id"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.brands.name') ?></th>
									<td width="60" data-item-field="name"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.brands.description') ?></th>
									<td data-item-field="description"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.brands.created_by') ?></th>
									<td data-item-field="created_by"></td>
								</tr>
								<tr>
									<th width="40"><?= lang('Main.brands.created_at') ?></th>
									<td width="60" data-item-field="created_at"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End of brand modal -->