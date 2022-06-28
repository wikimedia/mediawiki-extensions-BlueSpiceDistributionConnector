<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate\ImportDefaultPages;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class RunImportDefaultPages implements LoadExtensionSchemaUpdatesHook {

	/**
	 * @inheritDoc
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$maintenanceScript = new ImportDefaultPages();
		$maintenanceScript->execute();

		return true;
	}
}
