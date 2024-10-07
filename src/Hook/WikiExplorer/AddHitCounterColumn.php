<?php
namespace BlueSpice\DistributionConnector\Hook\WikiExplorer;

use MediaWiki\MediaWikiServices;

class AddHitCounterColumn {

	/**
	 * @param array &$rows
	 * @return bool
	 */
	public static function onBuildDataSets( &$rows ) {
		if ( !count( $rows ) ) {
			return true;
		}

		$pageIds = array_keys( $rows );
		$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()
			->getConnection( DB_REPLICA );
		$res = $dbr->select(
			'hit_counter',
			[ 'page_counter', 'page_id' ],
			[ 'page_id IN (' . implode( ',', $pageIds ) . ')' ],
			__METHOD__ );

		foreach ( $res as $row ) {
			$rows[$row->page_id]['page_hits'] = $row->page_counter;
		}

		foreach ( $rows as &$row ) {
			if ( !isset( $row['page_hits'] ) ) {
				$row['page_hits'] = 0;
			}
		}

		return true;
	}
}
