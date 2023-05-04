<?php

namespace BlueSpice\DistributionConnector\SearchBackend;

class SearchResultSet extends \SearchResultSet {
	/** @var int */
	public $index = -1;
	/** @inheritDoc */
	protected $results = [];

	/**
	 *
	 * @return int
	 */
	public function numRows() {
		return count( $this->results );
	}

	/**
	 *
	 * @return int
	 */
	public function getTotalHits() {
		return count( $this->results );
	}

	/**
	 *
	 * @return \SearchResult|false
	 */
	public function next() {
		$this->index++;
		if ( $this->index < count( $this->results ) ) {
			$nextResult = $this->results[$this->index];
		} else {
			return false;
		}
		return $nextResult;
	}

	public function rewind() {
		$this->index = -1;
	}

	/**
	 *
	 * @param \SearchResult $searchResult
	 */
	public function add( $searchResult ) {
		$this->results[] = $searchResult;
	}
}
