<?php

namespace BlueSpice\DistributionConnector\Data\Page\HitCounter;

use BlueSpice\Data\Page\Schema as PageSchema;
use MWStake\MediaWiki\Component\DataStore\FieldType;

class Schema extends \MWStake\MediaWiki\Component\DataStore\Schema {
	public const TABLE_NAME = PageSchema::TABLE_NAME;
	public const TABLE_NAME_JOIN = 'hit_counter';

	public function __construct() {
		parent::__construct( array_merge( (array)( new PageSchema ), [
			Record::COUNTER => [
				self::FILTERABLE => true,
				self::SORTABLE => true,
				self::TYPE => FieldType::INT
			],
		] ) );
	}
}
