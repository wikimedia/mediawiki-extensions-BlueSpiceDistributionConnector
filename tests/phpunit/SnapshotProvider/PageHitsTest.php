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
use PHPUnit\Framework\TestCase;
use stdClass;
use Wikimedia\Rdbms\IDatabase;
use Wikimedia\Rdbms\LoadBalancer;

class PageHitsTest extends TestCase {
	/** @var PageHits */
	private PageHits $pageHits;

	/** @var IDatabase */
	private IDatabase $dbMock;

	/** @var ISnapshotStore */
	private ISnapshotStore $snapshotStore;

	/** @var SnapshotFactory */
	private SnapshotFactory $snapshotFactory;

	public function setUp(): void {
		parent::setUp();

		$this->dbMock = $this->createMock( IDatabase::class );
		$loadBalancerMock = $this->createMock( LoadBalancer::class );
		$loadBalancerMock->method( 'getConnection' )->willReturn( $this->dbMock );
		$this->snapshotStore = $this->createMock( DatabaseStore::class );
		$this->snapshotFactory = new SnapshotFactory();
		$this->pageHits = new PageHits( $loadBalancerMock, $this->snapshotStore, $this->snapshotFactory );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers \BlueSpice\DistributionConnector\Statistics\SnapshotProvider\PageHits::generateSnapshot
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
				'a' => [
					'hits' => 1,
					'hitDiff' => 0
				],
				'b' => [
					'hits' => 0,
					'hitDiff' => 0
				],
				'c' => [
					'hits' => 4,
					'hitDiff' => 0
				],
				'd' => [
					'hits' => 0,
					'hitDiff' => 0
				],
				'e' => [
					'hits' => 1,
					'hitDiff' => 0
				],
			] ),
		);

		$snapshotDate = new SnapshotDate();
		$snapshot = $this->pageHits->generateSnapshot( $snapshotDate );

		$this->assertEquals( $snapshotDate, $snapshot->getDate() );
		$data = $snapshot->getData();
		$this->assertEquals( [
			'hits' => 3,
			'hitDiff' => 2,
			'growth' => 200.0
		], $data[ 'a' ] );
		// Hint: growth is zero because you cant express growth from 0 to 2 in percentage
		$this->assertEquals( [
			'hits' => 2,
			'hitDiff' => 2,
			'growth' => 0
		], $data[ 'b' ] );
		$this->assertEquals( [
			'hits' => 7,
			'hitDiff' => 3,
			'growth' => 75.0
		], $data[ 'c' ] );
		$this->assertEquals( [
			'hits' => 0,
			'hitDiff' => 0,
			'growth' => 0
		], $data[ 'd' ] );
		$this->assertEquals( [
			'hits' => 1,
			'hitDiff' => 0,
			'growth' => 0
		], $data[ 'e' ] );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers \BlueSpice\DistributionConnector\Statistics\SnapshotProvider\PageHits::generateSnapshot
	 */
	public function testGenerateSnapshotNegativeHitDiff(): void {
		$this->dbMock->method( 'select' )->willReturn( [
			$this->mockDatabasePageHit( 'a', 0, 1 )
		] );

		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [
					'hits' => 2,
					'hitDiff' => 0
				]
			] ),
		);

		$this->expectExceptionMessage( "Calculating hitDiff failed. hitDiff is negative." );

		$this->pageHits->generateSnapshot( new SnapshotDate() );
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @dataProvider intervalProvider
	 *
	 * @covers       \BlueSpice\DistributionConnector\Statistics\SnapshotProvider\PageHits::aggregate
	 */
	public function testAggregate( string $interval ): void {
		// Current hits
		$snapshots = [
			$this->mockSnapshot( [
				'a' => [
					'hits' => 1,
					'hitDiff' => 0
				],
				'b' => [
					'hits' => 0,
					'hitDiff' => 0
				],
				'c' => [
					'hits' => 3,
					'hitDiff' => 0
				]
			] ),
			$this->mockSnapshot( [
				'a' => [
					'hits' => 3,
					'hitDiff' => 2
				],
				'b' => [
					'hits' => 0,
					'hitDiff' => 0
				],
				'c' => [
					'hits' => 4,
					'hitDiff' => 1
				]
			] ),
			$this->mockSnapshot( [
				'a' => [
					'hits' => 99,
					'hitDiff' => 96
				],
				'b' => [
					'hits' => 0,
					'hitDiff' => 0
				],
				'c' => [
					'hits' => 5,
					'hitDiff' => 1
				]
			] ),
		];

		// Previous hits
		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [
					'hits' => 30,
					'hitDiff' => 0
				],
				'b' => [
					'hits' => 0,
					'hitDiff' => 0
				],
				'c' => [
					'hits' => 2,
					'hitDiff' => 0
				],
			] ),
		);

		$snapshot = $this->pageHits->aggregate( $snapshots, $interval, new SnapshotDate() );

		$data = $snapshot->getData();
		$this->assertEquals( [
			'hits' => 99,
			'hitDiff' => 69,
			'growth' => 229.99999999999997
		], $data[ 'a' ] );
		// Hint: growth is zero because you cant express growth from 0 to 2 in percentage
		$this->assertEquals( [
			'hits' => 0,
			'hitDiff' => 0,
			'growth' => 0.0
		], $data[ 'b' ] );
		$this->assertEquals( [
			'hits' => 5,
			'hitDiff' => 3,
			'growth' => 150.0
		], $data[ 'c' ] );
	}

	public function intervalProvider(): array {
		return [
			[ Snapshot::INTERVAL_WEEK ],
			[ Snapshot::INTERVAL_MONTH ],
			[ Snapshot::INTERVAL_YEAR ],
		];
	}

	/**
	 * @return void
	 * @throws Exception
	 *
	 * @covers \BlueSpice\DistributionConnector\Statistics\SnapshotProvider\PageHits::aggregate
	 */
	public function testAggregateNegativeHitDiff(): void {
		// Current hits
		$snapshots = [
			$this->mockSnapshot( [
				'a' => [
					'hits' => 1,
					'hitDiff' => 0
				],
			] ),
			$this->mockSnapshot( [
				'a' => [
					'hits' => 3,
					'hitDiff' => 2
				],
			] ),
			$this->mockSnapshot( [
				'a' => [
					'hits' => 4,
					'hitDiff' => 1
				],
			] ),
		];

		// Previous hits
		$this->snapshotStore->method( 'getPrevious' )->willReturn(
			$this->mockSnapshot( [
				'a' => [
					'hits' => 5,
					'hitDiff' => 0
				],
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
	 * @return Snapshot
	 */
	private function mockSnapshot( array $data ): Snapshot {
		return $this->snapshotFactory->createSnapshot(
			new SnapshotDate(),
			PageHitsSnapshot::TYPE,
			$data
		);
	}
}
