<?php

namespace SnapshotProvider;

use BlueSpice\DistributionConnector\Statistics\SnapshotProvider\PageHits;
use BlueSpice\ExtendedStatistics\ISnapshotStore;
use BlueSpice\ExtendedStatistics\PageHitsSnapshot;
use BlueSpice\ExtendedStatistics\Snapshot;
use BlueSpice\ExtendedStatistics\SnapshotDate;
use BlueSpice\ExtendedStatistics\SnapshotFactory;
use BlueSpice\ExtendedStatistics\SnapshotStore\DatabaseStore;
use Exception;
use IDatabase;
use PHPUnit\Framework\TestCase;
use stdClass;
use Wikimedia\Rdbms\LoadBalancer;

class PageHitsTest extends TestCase {
	/** @var PageHits */
	private PageHits $pageHits;

	/** @var IDatabase */
	private IDatabase $dbMock;

	/** @var ISnapshotStore */
	private ISnapshotStore $snapshotStore;

	public function setUp(): void {
		parent::setUp();

		$this->dbMock = $this->createMock( IDatabase::class );
		$loadBalancerMock = $this->createMock( LoadBalancer::class );
		$loadBalancerMock->method( 'getConnection' )->willReturn( $this->dbMock );
		$this->snapshotStore = $this->createMock( DatabaseStore::class );
		$this->pageHits = new PageHits( $loadBalancerMock, $this->snapshotStore, new SnapshotFactory() );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers PageHits::generateSnapshot
	 */
	public function testGenerateSnapshot(): void {
		// Current hits
		$this->dbMock->method( 'select' )->willReturn( [
			$this->mockDatabasePageHit( 'a', 3 ),
			$this->mockDatabasePageHit( 'b', 2 ),
			$this->mockDatabasePageHit( 'c', 7 ),
			$this->mockDatabasePageHit( 'd', 0 ),
			$this->mockDatabasePageHit( 'e', 1 ),
		] );

		// Previous hits
		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [ 'hits' => 1 ],
				'b' => [ 'hits' => 0 ],
				'c' => [ 'hits' => 4 ],
				'd' => [ 'hits' => 0 ],
				'e' => [ 'hits' => 1 ],
			] ),
		);

		$snapshotDate = new SnapshotDate();
		$snapshot = $this->pageHits->generateSnapshot( $snapshotDate );

		$this->assertEquals( $snapshotDate, $snapshot->getDate() );
		$data = $snapshot->getData();
		$this->assertEquals( $data['a'], [
			'hits' => 3,
			'hitDiff' => 2,
			'growth' => 200.0
		] );
		// Hint: growth is zero because you cant express growth from 0 to 2 in percentage
		$this->assertEquals( $data['b'], [
			'hits' => 2,
			'hitDiff' => 2,
			'growth' => 0
		] );
		$this->assertEquals( $data['c'], [
			'hits' => 7,
			'hitDiff' => 3,
			'growth' => 75.0
		] );
		$this->assertEquals( $data['d'], [
			'hits' => 0,
			'hitDiff' => 0,
			'growth' => 0
		] );
		$this->assertEquals( $data['e'], [
			'hits' => 1,
			'hitDiff' => 0,
			'growth' => 0
		] );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers PageHits::generateSnapshot
	 */
	public function testGenerateSnapshotNegativeHitDiff(): void {
		$this->dbMock->method( 'select' )->willReturn( [
			$this->mockDatabasePageHit( 'a', 0, 1 )
		] );

		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [ 'hits' => 2 ]
			] ),
		);

		$this->expectExceptionMessage( "Calculating hitDiff failed. hitDiff is negative." );

		$this->pageHits->generateSnapshot( new SnapshotDate() );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers PageHits::aggregate
	 */
	public function testAggregate(): void {
		// Current hits
		$snapshots = [
			$this->mockSnapshot( [
				'a' => [ 'hits' => 1 ],
				'b' => [ 'hits' => 0 ],
				'c' => [ 'hits' => 3 ]
			] ),
			$this->mockSnapshot( [
				'a' => [ 'hits' => 3 ],
				'b' => [ 'hits' => 0 ],
				'c' => [ 'hits' => 2 ]
			] ),
			$this->mockSnapshot( [
				'a' => [ 'hits' => 99 ],
				'b' => [ 'hits' => 0 ],
				'c' => [ 'hits' => 1 ]
			] ),
		];

		// Previous hits
		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [ 'hits' => 30 ],
				'b' => [ 'hits' => 0 ],
				'c' => [ 'hits' => 6 ],
			] ),
		);

		$snapshot = $this->pageHits->aggregate( $snapshots, Snapshot::INTERVAL_MONTH, new SnapshotDate() );

		$data = $snapshot->getData();
		$this->assertEquals( $data['a'], [
			'hits' => 103,
			'hitDiff' => 73,
			'growth' => 243.33333333333331
		] );
		// Hint: growth is zero because you cant express growth from 0 to 2 in percentage
		$this->assertEquals( $data['b'], [
			'hits' => 0,
			'hitDiff' => 0,
			'growth' => 0.0
		] );
		$this->assertEquals( $data['c'], [
			'hits' => 6,
			'hitDiff' => 0,
			'growth' => 0.0
		] );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers PageHits::aggregate
	 */
	public function testAggregateNegativeHitDiff(): void {
		// Current hits
		$snapshots = [
			$this->mockSnapshot( [
				'a' => [ 'hits' => 1 ]
			] ),
			$this->mockSnapshot( [
				'a' => [ 'hits' => 2 ]
			] ),
			$this->mockSnapshot( [
				'a' => [ 'hits' => 3 ]
			] ),
		];

		// Previous hits
		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [ 'hits' => 10 ]
			] ),
		);

		$this->expectExceptionMessage( "Calculating hitDiff failed. hitDiff is negative." );

		$this->pageHits->aggregate( $snapshots, Snapshot::INTERVAL_MONTH, new SnapshotDate() );
	}

	/**
	 * @param string $title
	 * @param int $hits
	 *
	 * @return stdClass
	 */
	private function mockDatabasePageHit( string $title, int $hits ): stdClass {
		$pageHitMock = $this->createMock( stdClass::class );
		$pageHitMock->page_title = $title;
		$pageHitMock->page_namespace = 0;
		$pageHitMock->page_counter = $hits;

		return $pageHitMock;
	}

	/**
	 * @param array $data
	 *
	 * @return PageHitsSnapshot
	 */
	private function mockSnapshot( array $data ): PageHitsSnapshot {
		return new PageHitsSnapshot( new SnapshotDate(), $data, Snapshot::INTERVAL_DAY );
	}
}
