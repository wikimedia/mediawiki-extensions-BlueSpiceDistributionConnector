<?php

use BlueSpice\DistributionConnector\EditionProvider;
use MediaWiki\MediaWikiServices;

return [
	'BlueSpiceEditionProvider' => static function ( MediaWikiServices $services ) {
		return new EditionProvider();
	},
];
