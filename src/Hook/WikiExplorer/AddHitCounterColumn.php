<?php
namespace BlueSpice\DistributionConnector\Hook\WikiExplorer;

class AddHitCounterColumn {

	/**
	 * @param array &$fields
	 * @return bool
	 */
	public static function onGetFieldDefinitions( array &$fields ) {
		$fields[] = [
			'name' => 'page_hits',
			'type' => 'int'
		];
		return true;
	}

	/**
	 * @param array &$columns
	 * @return bool
	 */
	public static function onGetColumnDefinitions( &$columns ) {
		$columns[] = [
			'header' => wfMessage(
				'bs-distributionconnector-hit-counter-wikiexplorer-column-name'
			)->plain(),
			'dataIndex' => 'page_hits'
		];
		return true;
	}

	/**
	 * @param array &$rows
	 * @return bool
	 */
	public static function onBuildDataSets( &$rows ) {
		if ( !count( $rows ) ) {
			return true;
		}

		$pageIds = array_keys( $rows );
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'hit_counter',
			[ 'page_counter', 'page_id' ],
			[ 'page_id IN (' . implode( ',', $pageIds ) . ')' ],
			__METHOD__ );

		foreach ( $res as $row ) {
			$rows[$row->page_id]['page_hits'] = $row->page_counter;
		}

		return true;
	}
}
