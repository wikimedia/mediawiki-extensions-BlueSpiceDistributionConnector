<?php

namespace BlueSpice\DistributionConnector\EntityConfig\Collection;

use BlueSpice\ExtendedStatistics\Data\Entity\Collection\Schema;
use BlueSpice\Data\FieldType;
use BlueSpice\ExtendedStatistics\EntityConfig\Collection;
use BlueSpice\DistributionConnector\Entity\Collection\HitCounter as Entity;

class HitCounter extends Collection {

	/**
	 *
	 * @return string
	 */
	protected function get_EntityClass() {
		return "\\BlueSpice\\DistributionConnector\\Entity\\Collection\\HitCounter";
	}

	/**
	 *
	 * @return array
	 */
	protected function get_AttributeDefinitions() {
		$attributes = array_merge( parent::get_AttributeDefinitions(), [
			Entity::ATTR_PAGE_TITLE => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::STRING,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
			Entity::ATTR_NUMBER_HITS => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
				Schema::PRIMARY => true,
			],
			Entity::ATTR_NUMBER_HITS_AGGREGATED => [
				Schema::FILTERABLE => true,
				Schema::SORTABLE => true,
				Schema::TYPE => FieldType::INT,
				Schema::INDEXABLE => true,
				Schema::STORABLE => true,
			],
		] );
		return $attributes;
	}

}
