<?php

namespace BlueSpice\DistributionConnector\Data\Page\HitCounter;

use BlueSpice\Data\Page\PrimaryDataProvider as PageDataProvider;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;

class PrimaryDataProvider extends PageDataProvider {

	/**
	 *
	 * @var ReaderParams
	 */
	protected $readerParams = null;

	/**
	 *
	 * @return array
	 */
	protected function getTableNames() {
		return [ Schema::TABLE_NAME, Schema::TABLE_NAME_JOIN ];
	}

	/**
	 *
	 * @param \stdClass $row
	 */
	protected function appendRowToData( \stdClass $row ) {
		$title = Title::newFromRow( $row );
		if ( !$title || !$this->userCanRead( $title ) ) {
			return;
		}

		$fields = [ Record::ID, Record::NS, Record::TITLE, Record::IS_REDIRECT,
			Record::ID_NEW, Record::TOUCHED, Record::LATEST, Record::CONTENT_MODEL,
			Record::COUNTER ];
		$data = [];
		foreach ( $fields as $key ) {
			$data[ $key ] = $row->{$key};
		}
		$record = new Record( (object)$data );
		MediaWikiServices::getInstance()->getHookContainer()->run(
			'BSPageStoreDataProviderBeforeAppendRow',
			[
				$this,
				$record,
				$title,
			]
		);
		if ( !$record ) {
			return;
		}
		$this->data[] = $record;
	}

	/**
	 *
	 * @return array
	 */
	protected function getDefaultConds() {
		return [ Record::CONTENT_MODEL => [ 'wikitext', '' ] ];
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return array
	 */
	protected function getJoinConds( ReaderParams $params ) {
		$prefix = $this->context->getConfig()->get( 'DBprefix' );
		return [
			$this->getTableNames()[0] => [ "RIGHT OUTER JOIN", [
				"$prefix{$this->getTableNames()[0]}.page_id = $prefix{$this->getTableNames()[1]}.page_id"
			] ]
		];
	}

}
