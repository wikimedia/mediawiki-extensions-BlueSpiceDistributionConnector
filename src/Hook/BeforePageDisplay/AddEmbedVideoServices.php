<?php

namespace BlueSpice\DistributionConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use ExtensionRegistry;

class AddEmbedVideoServices extends BeforePageDisplay {

	protected function skipProcessing() {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'EmbedVideo' ) ) {
			return true;
		}
		parent::skipProcessing();
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$services = \EmbedVideo\VideoService::getAvailableServices();
		$this->out->addJsConfigVars( [
			'bsgEmbedVideoServices' => $services
		] );

		return true;
	}

}
