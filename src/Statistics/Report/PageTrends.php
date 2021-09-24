<?php

namespace BlueSpice\DistributionConnector\Statistics\Report;

use BlueSpice\ExtendedStatistics\ClientReportHandler;

class PageTrends extends PageHits {
	/**
	 * @return string
	 */
	protected function getDataKeyToDisplay() {
		return 'growth';
	}

	/**
	 * @inheritDoc
	 */
	public function getClientReportHandler(): ClientReportHandler {
		return new ClientReportHandler(
			[ 'ext.bluespice.distributionconnector.statistics' ],
			'bs.distributionConnector.report.PageTrendsReport'
		);
	}
}
