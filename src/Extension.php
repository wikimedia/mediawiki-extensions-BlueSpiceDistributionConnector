<?php

namespace BlueSpice\DistributionConnector;

class Extension extends \BlueSpice\Extension {

	public static function onRegistration() {
		// disable edit-Link on HeaderTabs
		$GLOBALS['wgHeaderTabsEditTabLink'] = false;
	}
}
