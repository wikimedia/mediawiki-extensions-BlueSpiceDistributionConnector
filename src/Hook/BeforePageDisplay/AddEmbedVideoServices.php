<?php

namespace BlueSpice\DistributionConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use ExtensionRegistry;
use MediaWiki\Extension\EmbedVideo\EmbedService\EmbedServiceFactory;

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
		$services = EmbedServiceFactory::getAvailableServices();
		$this->out->addJsConfigVars( [
			'bsgEmbedVideoServices' => $services
		] );

		return true;
	}

}
