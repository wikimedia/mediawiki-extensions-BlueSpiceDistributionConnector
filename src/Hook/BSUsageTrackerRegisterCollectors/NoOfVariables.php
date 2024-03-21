<?php

namespace BlueSpice\DistributionConnector\Hook\BSUsageTrackerRegisterCollectors;

use BS\UsageTracker\Hook\BSUsageTrackerRegisterCollectors;

class NoOfVariables extends BSUsageTrackerRegisterCollectors {

	protected function doProcess() {
		$this->collectorConfig['no-of-variables'] = [
			'class' => 'Property',
			'config' => [
				'identifier' => 'variables',
				'internalDesc' => 'Number of pages with Variables'
			]
		];
	}
}
