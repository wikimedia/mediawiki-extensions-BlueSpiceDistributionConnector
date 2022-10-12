<?php

namespace BlueSpice\DistributionConnector\Statistics\SnapshotProvider;

use BlueSpice\ExtendedStatistics\ISnapshotProvider;
use BlueSpice\ExtendedStatistics\ISnapshotStore;
use BlueSpice\ExtendedStatistics\Snapshot;
use BlueSpice\ExtendedStatistics\SnapshotDate;
use Title;
use Wikimedia\Rdbms\LoadBalancer;

class PageHits implements ISnapshotProvider {
	/** @var LoadBalancer */
	private $loadBalancer;
	/** @var ISnapshotStore */
	private $snapshotStore;

	/**
	 * @param LoadBalancer $loadBalancer
	 * @param ISnapshotStore $store
	 */
	public function __construct( LoadBalancer $loadBalancer, ISnapshotStore $store ) {
		$this->loadBalancer = $loadBalancer;
		$this->snapshotStore = $store;
	}

	/**
	 * @param SnapshotDate $date
	 * @return Snapshot
	 */
	public function generateSnapshot( SnapshotDate $date ): Snapshot {
		$db = $this->loadBalancer->getConnection( DB_REPLICA );

		$previousSnapshot = $this->snapshotStore->getPrevious( clone $date, $this->getType() );

		$res = $db->select(
			[ 'h' => 'hit_counter', 'p' => 'page' ],
			[ 'h.page_id', 'p.page_title', 'p.page_namespace', 'h.page_counter' ],
			[ 'h.page_id = p.page_id' ],
			__METHOD__
		);

		$data = [];
		$totalHits = 0;
		foreach ( $res as $row ) {
			$title = Title::newFromRow( $row );
			$page = $title->getPrefixedDBkey();
			$hits = (int)$row->page_counter;
			$hitDiff = $hits;
			$growth = 0;
			if ( $previousSnapshot instanceof Snapshot ) {
				$previousHits = $this->getPreviousHits( $previousSnapshot, $page );
				if ( $previousHits === 0 ) {
					$growth = 100;
				} else {
					$hitDiff = $hits - $previousHits;
					$growth = ( $hitDiff / $previousHits ) * 100;
				}
			}
			$totalHits += $hitDiff;
			$data[$page] = [
				'hits' => $hits,
				'hitDiff' => $hitDiff,
				'growth' => $growth < 0 ? 0 : $growth
			];
		}
		$data['total'] = $totalHits;

		return new Snapshot( $date, $this->getType(), $data );
	}

	/**
	 * @inheritDoc
	 */
	public function aggregate(
		array $snapshots, $interval = Snapshot::INTERVAL_DAY, $date = null
	): Snapshot {
		$data = [];

		$previous = null;
		if ( $interval !== Snapshot::INTERVAL_DAY && $date ) {
			$previous = $this->snapshotStore->getPrevious(
				clone $date, $this->getType(), $interval
			);
		}

		foreach ( $snapshots as $snapshot ) {
			foreach ( $snapshot->getData() as $page => $props ) {
				if ( !isset( $data[$page] ) ) {
					$data[$page] = [
						'hits' => 0,
					];
				}
				$data[$page]['hits'] += $props['hits'];
				// Growth set below
				$data[$page]['growth'] = 0;
			}
		}
		if ( $previous instanceof Snapshot ) {
			foreach ( $data as $page => &$props ) {
				$previousHits = $this->getPreviousHits( $previous, $page );
				if ( $previousHits === 0 ) {
					$props['growth'] = 100;
				} else {
					$hitDiff = $props['hits'] - $previousHits;
					$props['growth'] = ( $hitDiff / $previousHits ) * 100;
				}
			}
		}

		return new Snapshot(
			$date ?? new SnapshotDate(), $this->getType(), $data, $interval
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getType() {
		return 'dc-pagehits';
	}

	/**
	 * @param Snapshot $previous
	 * @param string $page
	 * @return int
	 */
	private function getPreviousHits( Snapshot $previous, $page ) {
		$data = $previous->getData();
		if ( isset( $data[$page] ) ) {
			return $data[$page]['hits'];
		}

		return 0;
	}

	/**
	 * @param Snapshot $snapshot
	 * @return array
	 */
	public function getSecondaryData( Snapshot $snapshot ) {
		$db = $this->loadBalancer->getConnection( DB_REPLICA );
		$res = $db->select(
			[ 'p' => 'page', 'h' => 'hit_counter', 'cl' => 'categorylinks' ],
			[
				'p.page_id', 'p.page_title', 'p.page_namespace',
				'h.page_counter', 'GROUP_CONCAT( cl.cl_to ) as cats'
			],
			[],
			__METHOD__,
			[
				'GROUP BY' => 'p.page_id'
			],
			[
				'p' => [
					'INNER JOIN', [ 'p.page_id=h.page_id' ]
				],
				'cl' => [
					'LEFT OUTER JOIN', [ 'p.page_id=cl.cl_from' ]
				]
			]
		);

		$data = [];
		foreach ( $res as $row ) {
			$title = Title::newFromRow( $row );
			$page = $title->getPrefixedDBkey();
			$data[$page] = [
				'id' => (int)$row->page_id,
				'n' => (int)$row->page_namespace,
				'c' => is_string( $row->cats ) ? explode( ',', $row->cats ) : [],
				'h' => isset( $snapshotData[$page] ) ? $snapshotData[$page]['hits'] : 0,
				'g' => isset( $snapshotData[$page] ) ? $snapshotData[$page]['growth'] : 0,
			];
		}

		return $data;
	}
}
