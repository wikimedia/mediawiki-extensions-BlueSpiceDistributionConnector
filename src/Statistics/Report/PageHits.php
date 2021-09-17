<?php

namespace BlueSpice\DistributionConnector\Statistics\Report;

use BlueSpice\ExtendedStatistics\ClientReportHandler;
use BlueSpice\ExtendedStatistics\IReport;

class PageHits implements IReport {

	/**
	 * @inheritDoc
	 */
	public function getSnapshotKey() {
		return 'dc-pagehits';
	}

	/**
	 * @inheritDoc
	 */
	public function getClientData( $snapshots, array $filterData, $limit = 20 ): array {
		$filterForPage = $filterData['page'] ?? null;
		if ( empty( $filterForPage ) ) {
			return [];
		}
		$filterForPage = str_replace( ' ', '_', $filterForPage );
		$processed = [];

		foreach ( $snapshots as $snapshot ) {
			$data = $snapshot->getData();
			if ( !isset( $data[$filterForPage ] ) ) {
				continue;
			}
			$processed[] = [
				'name' => $snapshot->getDate()->forGraph(),
				'line' => $filterForPage,
				'value' => $data[$filterForPage][$this->getDataKeyToDisplay()]
			];
		}

		return $processed;
	}

	/**
	 * @return string
	 */
	protected function getDataKeyToDisplay() {
		return 'hits';
	}

	/**
	 * @inheritDoc
	 */
	public function getClientReportHandler(): ClientReportHandler {
		return new ClientReportHandler(
			[ 'ext.bluespice.distributionconnector.statistics' ],
			'bs.distributionConnector.report.PageHitsReport'
		);
	}
}
