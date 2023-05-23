<?php

namespace BlueSpice\DistributionConnector\Hook\LoadExtensionSchemaUpdates;

use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

class MigratePluggableAuthConfig implements LoadExtensionSchemaUpdatesHook {

	/**
	 * @inheritDoc
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->addPostDatabaseUpdateMaintenance(
			\BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate\MigratePluggableAuthConfig::class
		);
	}
}
