<?php

use BlueSpice\DistributionConnector\EditionProvider;
use BlueSpice\DistributionConnector\UserCounter;
use MediaWiki\MediaWikiServices;

return [
	'BlueSpiceEditionProvider' => static function ( MediaWikiServices $services ) {
		return new EditionProvider();
	},
	'BlueSpiceUserCounter' => static function ( MediaWikiServices $services ) {
		return new UserCounter(
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$services->getMainConfig(),
			$services->getDBLoadBalancer(),
			$services->getService( 'BlueSpiceEditionProvider' )
		);
	},
];
