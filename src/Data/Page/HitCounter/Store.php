<?php

namespace BlueSpice\DistributionConnector\Data\Page\HitCounter;

use BlueSpice\Data\Page\Store as PageStore;

class Store extends PageStore {

	/**
	 *
	 * @return Reader
	 */
	public function getReader() {
		return new Reader( $this->loadBalancer, $this->context );
	}

}
