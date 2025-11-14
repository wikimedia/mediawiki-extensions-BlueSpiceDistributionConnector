<?php

namespace BlueSpice\DistributionConnector\Integration\WikiFarm;

use BlueSpice\WikiFarm\CommandDescriptionBase;

class PluggableAuthMigrationCommand extends CommandDescriptionBase {

	/** @inheritDoc */
	public function getCommandArguments() {
		$maintenancePath = $this->buildMaintenancePath( 'BlueSpiceDistributionConnector' );
		return [ "$maintenancePath/MigratePluggableAuthSettings.php" ];
	}

	/** @inheritDoc */
	public function getPosition() {
		return 90;
	}

}
