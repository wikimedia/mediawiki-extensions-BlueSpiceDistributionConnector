<?php

namespace BlueSpice\DistributionConnector\SearchBackend;

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\TitleFactory;
use MWStake\MediaWiki\Component\CommonWebAPIs\Data\TitleQueryStore\Store;
use MWStake\MediaWiki\Component\CommonWebAPIs\Data\TitleQueryStore\TitleRecord;
use MWStake\MediaWiki\Component\DataStore\Filter;
use MWStake\MediaWiki\Component\DataStore\ReaderParams;
use RevisionSearchResult;
use SearchEngine;

class BlueSpiceTitleSearch extends SearchEngine {
	/** @var SearchEngine */
	private $fallbackSearchEngine;
	/** @var Store */
	private $store;
	/** @var TitleFactory */
	private $titleFactory;

	public function __construct() {
		$services = MediaWikiServices::getInstance();
		$this->store = new Store(
			$services->getDBLoadBalancer(),
			$services->getTitleFactory(),
			$services->getContentLanguage(),
			$services->getNamespaceInfo(),
			$services->getPageProps()
		);
		$this->titleFactory = $services->getTitleFactory();
		$fallbackClass = $services->getSearchEngineFactory()::getSearchEngineClass(
			$services->getConnectionProvider()
		);
		$this->fallbackSearchEngine = new $fallbackClass( $services->getConnectionProvider() );
	}

	/**
	 * @param string $term
	 *
	 * @return \ISearchResultSet|\Status|null
	 */
	public function searchText( $term ) {
		return $this->fallbackSearchEngine->searchText( $term );
	}

	/**
	 * @param int $limit
	 * @param int $offset
	 * @return void
	 */
	public function setLimitOffset( $limit, $offset = 0 ) {
		parent::setLimitOffset( $limit, $offset );
		$this->fallbackSearchEngine->setLimitOffset( $limit, $offset );
	}

	/**
	 *
	 * @param string $term
	 * @return SearchResultSet
	 */
	public function searchTitle( $term ) {
		if ( $term === '*' ) {
			$term = '';
		}
		[ $titles, $total ] = $this->search( $term );
		$searchResultSet = new SearchResultSet( $total );
		foreach ( $titles as $title ) {
			$searchResultSet->add(
				new RevisionSearchResult( $title )
			);
		}

		return $searchResultSet;
	}

	/**
	 * @param string $term
	 * @param bool|null $mustStartWithTerm
	 *
	 * @return array
	 */
	private function search( $term, ?bool $mustStartWithTerm = false ) {
		$term = trim( $term );

		$params = [
			'limit' => $this->limit,
			'start' => $this->offset,
		];
		if ( is_array( $this->namespaces ) && !empty( $this->namespaces ) ) {
			$params['filter'] = [
				[
					'type' => 'list',
					'value' => $this->namespaces,
					'operator' => 'in',
					'property' => 'namespace',
				]
			];
		}
		if ( $term ) {
			if ( $mustStartWithTerm ) {
				$params['filter'][] = [
					'type' => 'string',
					'value' => $term,
					'operator' => Filter\StringValue::COMPARISON_STARTS_WITH,
					'property' => 'title',
				];
			} else {
				$params['query'] = $term;
			}
		}
		$params = new ReaderParams( $params );
		$res = $this->store->getReader()->read( $params );
		$titles = [];
		foreach ( $res->getRecords() as $record ) {
			$titles[] = $this->titleFactory->makeTitleSafe(
				$record->get( TitleRecord::PAGE_NAMESPACE ),
				$record->get( TitleRecord::PAGE_DBKEY )
			);
		}

		return [ array_filter( $titles ), $res->getTotal() ];
	}

	/**
	 *
	 * @param string $search
	 * @return \SearchSuggestionSet
	 */
	protected function completionSearchBackend( $search ) {
		[ $titles, $total ] = $this->search( trim( $search ), true );
		return \SearchSuggestionSet::fromTitles( $titles );
	}

	/**
	 *
	 * @param int $id
	 * @param string $title
	 * @param string $text
	 */
	public function update( $id, $title, $text ) {
		$this->fallbackSearchEngine->update( $id, $title, $text );
	}

	/**
	 *
	 * @param int $id
	 * @param string $title
	 */
	public function updateTitle( $id, $title ) {
		$this->fallbackSearchEngine->updateTitle( $id, $title );
	}

	/**
	 *
	 * @param int $id
	 * @param string $title
	 */
	public function delete( $id, $title ) {
		$this->fallbackSearchEngine->delete( $id, $title );
	}
}
