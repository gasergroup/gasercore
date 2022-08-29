<?php namespace App\Controllers\Frontend;

class Dashboard extends BaseController {

	public function index() {
		$this->data['route'] = 'dashboard';

		$this->applyUpdates();

		if($this->logged_user->role == 'admin' || $this->logged_user->role == 'supervisor')
			return view('dashboard', $this->data);
		else
			return view('worker_dashboard', $this->data);
	}

	private function applyUpdates() {
		// If current version is < 1.0.6, apply 1.0.6 update
		$currentVersion = $this->data['settings']->version;

		if($currentVersion == '1.0.0' || $currentVersion == '1.0.1' || $currentVersion == '1.0.2' || $currentVersion == '1.0.3' || $currentVersion == '1.0.4' || $currentVersion == '1.0.5')
			$currentVersion = $this->_apply_106_update();

		if($currentVersion == '1.0.6')
			$currentVersion = $this->_apply_107_update();

		$this->data['settings']->version = $currentVersion;
	}

	private function _apply_106_update() {
		$this->settings->set('val', '1.0.6')->where('name', 'version')->update();
		return '1.0.6';
	}

	private function _apply_107_update() {
		$adjustmentsTable = "CREATE TABLE IF NOT EXISTS `inventov2_transfers` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`from_warehouse_id` int(11) NOT NULL,
			`to_warehouse_id` int(11) NOT NULL,
			`items` text NOT NULL,
			`notes` text NOT NULL,
			`created_by` int(11) NOT NULL,
			`created_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
			`updated_at` datetime NOT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `id` (`id`),
			KEY `warehouse_id` (`from_warehouse_id`) USING BTREE,
			KEY `FK_inventov2_transfers_inventov2_warehouses2` (`to_warehouse_id`),
			CONSTRAINT `FK_inventov2_transfers_inventov2_warehouses` FOREIGN KEY (`from_warehouse_id`) REFERENCES `inventov2_warehouses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
			CONSTRAINT `FK_inventov2_transfers_inventov2_warehouses2` FOREIGN KEY (`to_warehouse_id`) REFERENCES `inventov2_warehouses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
		)";

		$db = db_connect();
		$db->query($adjustmentsTable);
		$this->settings->set('val', '1.0.7')->where('name', 'version')->update();
		return '1.0.7';
	}
}