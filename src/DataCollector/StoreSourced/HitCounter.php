<?php

namespace BlueSpice\DistributionConnector\DataCollector\StoreSourced;

use Config;
use RequestContext;
use BlueSpice\Services;
use BlueSpice\Data\IRecord;
use BlueSpice\Data\IStore;
use BlueSpice\EntityFactory;
use BlueSpice\ExtendedStatistics\SnapshotFactory;
use BlueSpice\ExtendedStatistics\Entity\Snapshot;
use BlueSpice\ExtendedStatistics\DataCollector\StoreSourced;
use BlueSpice\ExtendedStatistics\Entity\Collection as BaseCollection;
use BlueSpice\DistributionConnector\Data\Page\HitCounter\Store;
use BlueSpice\DistributionConnector\Data\Page\HitCounter\Record;
use BlueSpice\DistributionConnector\Entity\Collection\HitCounter as Collection;

class HitCounter extends StoreSourced {

	/**
	 *
	 * @var SnapshotFactory
	 */
	protected $snapshotFactory = null;

	/**
	 *
	 * @var array
	 */
	protected $namespaces = null;

	/**
	 *
	 * @var Collection[]
	 */
	protected $lastCollection = null;

	/**
	 *
	 * @param string $type
	 * @param Snapshot $snapshot
	 * @param Config $config
	 * @param EntityFactory $factory
	 * @param IStore $store
	 * @param SnapshotFactory $snapshotFactory
	 * @param array $namespaces
	 */
	protected function __construct( $type, Snapshot $snapshot, Config $config,
		EntityFactory $factory, IStore $store, SnapshotFactory $snapshotFactory,
		array $namespaces ) {
		parent::__construct( $type, $snapshot, $config, $factory, $store );
		$this->snapshotFactory = $snapshotFactory;
		$this->namespaces = $namespaces;
	}

	/**
	 *
	 * @param string $type
	 * @param Services $services
	 * @param Snapshot $snapshot
	 * @param Config|null $config
	 * @param EntityFactory|null $factory
	 * @param IStore|null $store
	 * @param SnapshotFactory|null $snapshotFactory
	 * @param array|null $namespaces
	 * @return DataCollector
	 */
	public static function factory( $type, Services $services, Snapshot $snapshot,
		Config $config = null, EntityFactory $factory = null, IStore $store = null,
		SnapshotFactory $snapshotFactory = null, array $namespaces = null ) {
		if ( !$config ) {
			$config = $snapshot->getConfig();
		}
		if ( !$factory ) {
			$factory = $services->getService( 'BSEntityFactory' );
		}
		if ( !$snapshotFactory ) {
			$snapshotFactory = $services->getService(
				'BSExtendedStatisticsSnapshotFactory'
			);
		}
		if ( !$store ) {
			$context = RequestContext::getMain();
			$context->setUser(
				$services->getService( 'BSUtilityFactory' )->getMaintenanceUser()->getUser()
			);
			$store = new Store( $context, $services->getDBLoadBalancer() );
		}
		if ( !$namespaces ) {
			$version = $snapshot->getConfig()->get( 'Version' );
			if ( false && version_compare( $version, '1.34', '>=' ) ) {
				$namespaces = $services->getNamespaceInfo()->getContentNamespaces();
			} else {
				$namespaces = \MWNamespace::getCanonicalNamespaces();
			}
			foreach ( $namespaces as $idx => $canonical ) {
				if ( $idx >= 0 ) {
					continue;
				}
				unset( $namespaces[$idx] );
			}
		}
		return new static(
			$type,
			$snapshot,
			$config,
			$factory,
			$store,
			$snapshotFactory,
			$namespaces
		);
	}

	/**
	 *
	 * @param IRecord $record
	 * @return \stdClass
	 */
	protected function map( IRecord $record ) {
		return (object)[
			Collection::ATTR_TYPE => Collection::TYPE,
			Collection::ATTR_PAGE_TITLE => $record->get( Record::TITLE ),
			Collection::ATTR_TIMESTAMP_CREATED => $this->snapshot->get(
				Snapshot::ATTR_TIMESTAMP_CREATED
			),
			Collection::ATTR_NUMBER_HITS_AGGREGATED => $record->get( Record::COUNTER ),
			Collection::ATTR_NUMBER_HITS => $record->get( Collection::ATTR_NUMBER_HITS ),
		];
	}

	/**
	 *
	 * @return RecordSet
	 */
	protected function doCollect() {
		$res = parent::doCollect();
		$lastHits = [];
		foreach ( $this->getLastCollection() as $collection ) {
			$lastHits[$collection->get( Collection::ATTR_PAGE_TITLE )] = $collection->get(
				Collection::ATTR_NUMBER_HITS_AGGREGATED
			);
		}
		foreach ( $res->getRecords() as $record ) {
			if ( !empty( $this->namespaces[(int)$record->get( Record::NS )] ) ) {
				$record->set(
					Record::TITLE,
					$this->namespaces[(int)$record->get( Record::NS )] . ':' . $record->get( Record::TITLE )
				);
			}
			$record->set( Collection::ATTR_NUMBER_HITS, 0 );
			if ( !isset( $lastHits[$record->get( Record::TITLE )] ) ) {
				$record->set(
					Collection::ATTR_NUMBER_HITS,
					$record->get( Record::COUNTER, 0 )
				);
				continue;
			}
			$record->set(
				Collection::ATTR_NUMBER_HITS,
				$record->get( Record::COUNTER, 0 ) - $lastHits[$record->get( Record::TITLE )]
			);
		}

		return $res;
	}

	/**
	 *
	 * @return Collection[]
	 */
	protected function getLastCollection() {
		if ( $this->lastCollection !== null ) {
			return $this->lastCollection;
		}
		$this->lastCollection = [];
		$snapshot = $this->snapshotFactory->getPrevious( $this->snapshot );
		if ( !$snapshot ) {
			return $this->lastCollection;
		}
		$this->lastCollection = array_filter(
			$snapshot->get( Snapshot::ATTR_COLLECTION ),
			function ( BaseCollection $e ) {
			return $e instanceof Collection;
		 } );
		return $this->lastCollection;
	}
}
