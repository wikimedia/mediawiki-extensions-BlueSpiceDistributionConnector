<?php

namespace BlueSpice\DistributionConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use MediaWiki\Extension\EmbedVideo\EmbedService\EmbedServiceFactory;
use MediaWiki\Registration\ExtensionRegistry;
use ReflectionClass;
use ReflectionException;

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
	 * @throws ReflectionException
	 */
	protected function doProcess(): bool {
		$services = EmbedServiceFactory::getAvailableServices();
		$this->out->addJsConfigVars( [
			'bsgEmbedVideoServices' => array_map( static function ( $serviceClass ) {
				$reflect = new ReflectionClass( $serviceClass );
				return $reflect->getShortName();
			}, $services )
		] );

		return true;
	}

}
