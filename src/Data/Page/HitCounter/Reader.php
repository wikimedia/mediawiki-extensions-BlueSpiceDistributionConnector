<?php

namespace BlueSpice\DistributionConnector\Data\Page\HitCounter;

use IContextSource;
use Wikimedia\Rdbms\LoadBalancer;
use BlueSpice\Data\ReaderParams;
use BlueSpice\Data\Page\Reader as PageReader;

class Reader extends PageReader {
	/**
	 *
	 * @param LoadBalancer $loadBalancer
	 * @param IContextSource|null $context
	 */
	public function __construct( $loadBalancer, IContextSource $context = null ) {
		parent::__construct( $loadBalancer, $context, $context->getConfig() );
	}

	/**
	 *
	 * @param ReaderParams $params
	 * @return PrimaryDataProvider
	 */
	protected function makePrimaryDataProvider( $params ) {
		return new PrimaryDataProvider( $this->db, $this->getSchema(), $this->context );
	}

	/**
	 *
	 * @return null
	 */
	protected function makeSecondaryDataProvider() {
		return null;
	}

	/**
	 *
	 * @return Schema
	 */
	public function getSchema() {
		return new Schema();
	}

}
