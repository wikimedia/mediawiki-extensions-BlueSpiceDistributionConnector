<?php

namespace BlueSpice\DistributionConnector\Hook\OutputPageBodyAttributes;

use MediaWiki\Output\OutputPage;
use Skin;

class InitRcFilters {

	/**
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @param array &$bodyAttrs
	 * @return void
	 */
	public static function onOutputPageBodyAttributes( OutputPage $out, Skin $skin, &$bodyAttrs ) {
		// NOTE: This solution is not intended to be permanent and is only a temporary fix.
		$bodyAttrs[ 'class' ] .= ' mw-rcfilters-ui-initialized';
	}
}
