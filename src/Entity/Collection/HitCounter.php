<?php

namespace BlueSpice\DistributionConnector\Entity\Collection;

use BlueSpice\ExtendedStatistics\Entity\Collection;

class HitCounter extends Collection {
	const TYPE = 'hitcounter';

	const ATTR_NUMBER_HITS = 'numberhits';
	const ATTR_NUMBER_HITS_AGGREGATED = 'numberhitsaggregated';
	const ATTR_PAGE_TITLE = 'pagetitle';
}
