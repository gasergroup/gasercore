<div id="navbar-logo" class="d-flex align-items-center">
	<a href="<?= base_url() ?>">
		<img src="<?= base_url('assets/images/logo/logo.png') ?>" class="ml-4">
	</a>
</div>

<div id="navbar" class="d-flex align-items-center">
	<div class="left">
		<a href="#">
			<i class="fas fa-align-justify"></i>
		</a>
	</div>

	<div class="right d-flex align-items-center flex-grow-1 justify-content-end">
		<?php if($logged_user->role == 'admin') { ?>
		<div class="dropdown notifications-badge">
			<button type="button" data-toggle="dropdown" class="dropdown-toggle dropdown-toggle-no-caret">
				<i class="fa fa-bell"></i>
				<?php
				if($alerts_count > 9)
					echo '<span class="badge">9+</span>';
				else
					echo '<span class="badge">' . $alerts_count . '</span>';
				?>
			</button>
			<ul class="dropdown-menu">
				<?php if(count($alerts) > 0) { ?>
				<?php foreach($alerts as $alert) { ?>
				<li>
					<a href="<?= base_url('alerts') ?>" class="dropdown-item">
						<?php
						if($alert['type'] == 'min') {
							echo lang('Main.dashboard.alerts.min_alert', [
								'current_qty' => $alert['current_qty'],
								'item_name' => $alert['item']['name'],
								'warehouse_name' => $alert['warehouse']['name'],
								'minimum_alert' => $alert['item']['min_alert']
							]);
						}else{
							echo lang('Main.dashboard.alerts.max_alert', [
								'current_qty' => $alert['current_qty'],
								'item_name' => $alert['item']['name'],
								'warehouse_name' => $alert['warehouse']['name'],
								'maximum_alert' => $alert['item']['max_alert']
							]);
						}
						?>
					</a>
				</li>
				<?php } ?>
				<li class="see-more">
					<a href="<?= base_url('alerts') ?>" class="dropdown-item">
						<?= lang('Main.dashboard.alerts.see_more') ?>
					</a>
				</li>
				<?php }else{ ?>
				<li class="no-alerts">
				<?= lang('Main.dashboard.alerts.no_alerts') ?>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>

		<div class="dropdown logged-user btn-group">
			<button type="button" data-toggle="dropdown" class="btn dropdown-toggle btn-outline-primary btn-sm text-decoration-none dropdown-toggle-no-caret">
				<?= $logged_user->name; ?>
			</button>
			
			<ul role="menu" class="dropdown-menu dropdown-menu-right">
				<li>
					<a href="<?= base_url('logout') ?>" class="dropdown-item">
						<i class="fas fa-sign-out-alt"></i>
						<?= lang('Main.logout') ?>
					</a>
				</li>
			</ul>
		</div>

		<div class="dropdown lang btn-group">
			<button type="button" data-toggle="dropdown" class="btn dropdown-toggle btn-secondary btn-sm text-decoration-none dropdown-toggle-no-caret">
				<?= $current_locale ?>
			</button>

			<ul role="menu" class="dropdown-menu dropdown-menu-right">
				<?php foreach($locales as $locale) { ?>
				<li>
					<a href="#" data-locale="<?= $locale ?>" class="dropdown-item"><?= $locale ?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>