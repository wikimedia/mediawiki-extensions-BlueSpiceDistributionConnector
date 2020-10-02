<?php

namespace BlueSpice\DistributionConnector\Hook\LoadExtensionSchemaUpdates;

use BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate\AddTemplateUserPageStandardContent as PostDatabaseUpdateAddTemplateUserPageStandardContent;
use BlueSpice\Hook\LoadExtensionSchemaUpdates;

class AddTemplateUserPageStandardContent extends LoadExtensionSchemaUpdates {

	protected function doProcess() {
		$this->updater->addPostDatabaseUpdateMaintenance(
			PostDatabaseUpdateAddTemplateUserPageStandardContent::class
		);

		return true;
	}

}
