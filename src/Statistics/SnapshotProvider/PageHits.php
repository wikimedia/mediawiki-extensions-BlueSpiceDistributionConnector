<?php

namespace BlueSpice\DistributionConnector\Statistics\SnapshotProvider;

use BlueSpice\ExtendedStatistics\ISnapshotProvider;
use BlueSpice\ExtendedStatistics\ISnapshotStore;
use BlueSpice\ExtendedStatistics\PageHitsSnapshot;
use BlueSpice\ExtendedStatistics\Snapshot;
use BlueSpice\ExtendedStatistics\SnapshotDate;
use BlueSpice\ExtendedStatistics\SnapshotFactory;
use Exception;
use MediaWiki\Title\Title;
use Wikimedia\Rdbms\LoadBalancer;

class PageHits implements ISnapshotProvider {
	/** @var LoadBalancer */
	private $loadBalancer;
	/** @var ISnapshotStore */
	private $snapshotStore;

	/** @var SnapshotFactory */
	private SnapshotFactory $snapshotFactory;

	/**
	 * @param LoadBalancer $loadBalancer
	 * @param ISnapshotStore $store
	 * @param SnapshotFactory $snapshotFactory
	 */
	public function __construct(
		LoadBalancer $loadBalancer,
		ISnapshotStore $store,
		SnapshotFactory $snapshotFactory
	) {
		$this->loadBalancer = $loadBalancer;
		$this->snapshotStore = $store;
		$this->snapshotFactory = $snapshotFactory;
	}

	/**
	 * @param SnapshotDate $date
	 *
	 * @return Snapshot
	 * @throws Exception
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
		foreach ( $res as $row ) {
			$title = Title::newFromRow( $row );
			$page = $title->getPrefixedDBkey();
			$hits = (int)$row->page_counter;
			$data[$page] = [
				'hits' => $hits,
				'hitDiff' => $this->calcHitDiff( $hits, $previousSnapshot, $page )
			];
		}

		return $this->snapshotFactory->createSnapshot(
			$date, $this->getType(), $data
		);
	}

	/**
	 * @inheritDoc
	 * @throws Exception
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
				// Initialize array for page if not exists
				if ( !isset( $data[$page] ) ) {
					$data[$page] = [
						'hitDiff' => 0,
					];
				}

				$data[$page]['hitDiff'] += $props['hitDiff'];
				$data[$page]['hits'] = $data[$page]['hitDiff'];

				if ( $previous ) {
					$data[$page]['hits'] += $previous->getData()[$page]['hits'];
				}
			}
		}

		return $this->snapshotFactory->createSnapshot(
			$date ?? new SnapshotDate(),
			$this->getType(),
			$data,
			$interval
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getType() {
		return PageHitsSnapshot::TYPE;
	}

	/**
	 * hitDiff must not be negative
	 *
	 * @param int $totalHits
	 * @param Snapshot|null $previous
	 * @param string $page
	 *
	 * @return int
	 * @throws Exception
	 */
	private function calcHitDiff( int $totalHits, ?Snapshot $previous, string $page ): int {
		if ( !( $previous instanceof Snapshot ) ) {
			return $totalHits;
		}

		$hitDiff = $totalHits - $this->getPreviousHits( $previous, $page );

		if ( $hitDiff < 0 ) {
			throw new Exception( 'Calculating hitDiff failed. hitDiff is negative.' );
		}

		return $hitDiff;
	}

	/**
	 * @param Snapshot $previous
	 * @param string $page
	 * @return int
	 */
	private function getPreviousHits( Snapshot $previous, string $page ): int {
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
