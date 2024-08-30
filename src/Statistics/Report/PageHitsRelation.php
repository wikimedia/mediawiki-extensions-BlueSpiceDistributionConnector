<?php

namespace BlueSpice\DistributionConnector\Statistics\Report;

use BlueSpice\ExtendedStatistics\ClientReportHandler;

class PageHitsRelation extends PageHits {

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
			$total = $snapshot->calcTotal();
			if ( !isset( $data[$filterForPage ] ) ) {
				continue;
			}
			$value = $data[$filterForPage][$this->getDataKeyToDisplay()] / $total;
			if ( $value === null ) {
				if ( $total === 0 ) {
					continue;
				}
				$value = $data[$filterForPage]['hits'] / $total;
			}
			$processed[] = [
				'name' => $snapshot->getDate()->forGraph(),
				'line' => $filterForPage,
				'value' => number_format( $value, 2 ) * 100
			];
		}

		return $processed;
	}

	/**
	 * @return string
	 */
	protected function getDataKeyToDisplay() {
		return 'hitDiff';
	}

	/**
	 * @inheritDoc
	 */
	public function getClientReportHandler(): ClientReportHandler {
		return new ClientReportHandler(
			[ 'ext.bluespice.distributionconnector.statistics' ],
			'bs.distributionConnector.report.PageHitsRelationReport'
		);
	}
}
