<?php

namespace BlueSpice\DistributionConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;

class AddHitCounters extends BeforePageDisplay {

	protected function skipProcessing() {
		if ( !class_exists( "\HitCounters\HitCounters" ) ) {
			return true;
		}
		parent::skipProcessing();
	}

	protected function doProcess() {
		$viewCount = \HitCounters\HitCounters::getCount( $this->skin->getTitle() );
		$this->out->addJsConfigVars( [
			'bsgHitCountersSitetools' => $viewCount
		] );
	}

}
