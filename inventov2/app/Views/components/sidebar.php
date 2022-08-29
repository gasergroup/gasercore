<div id="sidebar">
	<ul class="nav">
		<li>
			<a href="<?= base_url() ?>"<?php if($route == 'dashboard') echo ' class="active"' ?>>
				<i class="fas fa-tachometer-alt"></i>
				<span><?= lang('Main.sidebar.dashboard') ?></span>
			</a>
		</li>

		<li>
			<a href="<?= base_url('items') ?>"<?php if($route == 'items') echo ' class="active"' ?>>
				<i class="fas fa-barcode"></i>
				<span><?= lang('Main.sidebar.items') ?></span>
			</a>
		</li>

		<li class="dropdown<?php if($route == 'purchases' || $route == 'purchases-returns') echo ' open' ?>">
			<a href="#">
				<i class="fas fa-handshake"></i>
				<span><?= lang('Main.sidebar.purchases') ?></span>
				<span>
					<i class="fas fa-caret-down fa-w-6 dropdown-status"></i>
				</span>
			</a>
			<div class="dropdown-content">
				<ul>
				<li>
						<a href="<?= base_url('purchases') ?>"<?php if($route == 'purchases') echo ' class="active"' ?>>
						<?= lang('Main.sidebar.purchases') ?>
						</a>
					</li>
					<li>
						<a href="<?= base_url('purchases/returns') ?>"<?php if($route == 'purchases-returns') echo ' class="active"' ?>>
						<?= lang('Main.sidebar.purchases_returns') ?>
						</a>
					</li>
				</ul>
			</div>
		</li>

		<li class="dropdown<?php if($route == 'sales' || $route == 'sales-returns') echo ' open' ?>">
			<a href="#">
				<i class="fas fa-money-bill"></i>
				<span><?= lang('Main.sidebar.sales') ?></span>
				<span>
					<i class="fas fa-caret-down fa-w-6 dropdown-status"></i>
				</span>
			</a>
			<div class="dropdown-content">
				<ul>
				<li>
						<a href="<?= base_url('sales') ?>"<?php if($route == 'sales') echo ' class="active"' ?>>
						<?= lang('Main.sidebar.sales') ?>
						</a>
					</li>
					<li>
						<a href="<?= base_url('sales/returns') ?>"<?php if($route == 'sales-returns') echo ' class="active"' ?>>
						<?= lang('Main.sidebar.sales_returns') ?>
						</a>
					</li>
				</ul>
			</div>
		</li>

		<?php if($logged_user->role == 'supervisor' || $logged_user->role == 'admin') { ?>
		<li>
			<a href="<?= base_url('adjustments') ?>"<?php if($route == 'adjustments') echo ' class="active"' ?>>
				<i class="fas fa-clipboard-list"></i>
				<span><?= lang('Main.sidebar.adjustments') ?></span>
			</a>
		</li>

		<li>
			<a href="<?= base_url('transfers') ?>"<?php if($route == 'transfers') echo ' class="active"' ?>>
				<i class="fas fa-exchange-alt"></i>
				<span><?= lang('Main.sidebar.transfers') ?></span>
			</a>
		</li>
		<?php } ?>

		<li>
			<a href="<?= base_url('suppliers') ?>"<?php if($route == 'suppliers') echo ' class="active"' ?>>
				<i class="fas fa-industry"></i>
				<span><?= lang('Main.sidebar.suppliers') ?></span>
			</a>
		</li>

		<li>
			<a href="<?= base_url('customers') ?>"<?php if($route == 'customers') echo ' class="active"' ?>>
				<i class="fas fa-user-tag"></i>
				<span><?= lang('Main.sidebar.customers') ?></span>
			</a>
		</li>

		<li>
			<a href="<?= base_url('categories') ?>"<?php if($route == 'categories') echo ' class="active"' ?>>
				<i class="fas fa-tag"></i>
				<span><?= lang('Main.sidebar.categories') ?></span>
			</a>
		</li>

		<li>
			<a href="<?= base_url('brands') ?>"<?php if($route == 'brands') echo ' class="active"' ?>>
				<i class="fas fa-certificate"></i>
				<span><?= lang('Main.sidebar.brands') ?></span>
			</a>
		</li>

		<?php if($logged_user->role == 'supervisor' || $logged_user->role == 'admin') { ?>
		<li>
			<a href="<?= base_url('warehouses') ?>"<?php if($route == 'warehouses') echo ' class="active"' ?>>
				<i class="fas fa-warehouse"></i>
				<span><?= lang('Main.sidebar.warehouses') ?></span>
			</a>
		</li>
		<?php } ?>

		<?php if($logged_user->role == 'admin') { ?>
			<li>
			<a href="<?= base_url('users') ?>"<?php if($route == 'users') echo ' class="active"' ?>>
				<i class="fas fa-user"></i>
				<span><?= lang('Main.sidebar.users') ?></span>
			</a>
		</li>
		
		<li>
			<a href="<?= base_url('alerts') ?>"<?php if($route == 'alerts') echo ' class="active"' ?>>
				<i class="fa fa-bell"></i>
				<span><?= lang('Main.sidebar.alerts') ?></span>
			</a>
		</li>

		<li>
			<a href="<?= base_url('settings') ?>"<?php if($route == 'settings') echo ' class="active"' ?>>
				<i class="fas fa-cog"></i>
				<span><?= lang('Main.sidebar.settings') ?></span>
			</a>
		</li>
		<?php } ?>
	</ul>
</div>