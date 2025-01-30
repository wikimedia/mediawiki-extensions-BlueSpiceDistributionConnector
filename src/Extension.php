<?php

namespace BlueSpice\DistributionConnector;

use BlueSpice\DistributionConnector\SearchBackend\BlueSpiceTitleSearch;
use MediaWiki\Registration\ExtensionRegistry;

class Extension extends \BlueSpice\Extension {

	public static function onRegistration() {
		// disable edit-Link on HeaderTabs
		$GLOBALS['wgHeaderTabsEditTabLink'] = false;
		if ( empty( $GLOBALS['bsgDistributionConnectorEventBusEventServices'] )
			&& !empty( $GLOBALS['wgEventServices'] ) ) {
			// The config must have something defined else it will throw an exception:
			// InvalidArgumentException from line 125 of
			// ...EventBus/includes/EventBusFactory.php: Could not get EventBus instance
			// for event service 'eventbus'. This event service name must exist in
			// EventServices config with a url setting.
			$GLOBALS['bsgDistributionConnectorEventBusEventServices']
				= $GLOBALS['wgEventServices'];
		}

		$GLOBALS['wgSearchType'] = BlueSpiceTitleSearch::class;

		if ( ExtensionRegistry::getInstance()->isLoaded( 'EmbedVideo' ) ) {
			$GLOBALS['wgContentDropletsDroplets']['video'] = [
				"class" => "\\BlueSpice\\DistributionConnector\\ContentDroplets\\VideoDroplet"
			];
		}
		if ( ExtensionRegistry::getInstance()->isLoaded( 'InputBox' ) ) {
			$GLOBALS['wgContentDropletsDroplets']['createInput'] = [
				"class" => "\\BlueSpice\\DistributionConnector\\ContentDroplets\\CreateInputDroplet"
			];
		}
		if ( ExtensionRegistry::getInstance()->isLoaded( 'PDFCreator' ) ) {
			$GLOBALS['wgContentDropletsDroplets']['pdflink'] = [
				"class" => "\\BlueSpice\\DistributionConnector\\ContentDroplets\\PDFLinkDroplet"
			];
		}
	}
}
