<?php

namespace BlueSpice\DistributionConnector\Hook\LoadExtensionSchemaUpdates;

use BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate\AddTemplateUserPageStandardContent as PostDatabaseUpdateAddTemplateUserPageStandardContent; // phpcs:ignore Generic.Files.LineLength.TooLong
use BlueSpice\Hook\LoadExtensionSchemaUpdates;

class AddTemplateUserPageStandardContent extends LoadExtensionSchemaUpdates {

	/**
	 * @return bool
	 */
	protected function doProcess() {
		$this->updater->addPostDatabaseUpdateMaintenance(
			PostDatabaseUpdateAddTemplateUserPageStandardContent::class
		);

		return true;
	}

}
