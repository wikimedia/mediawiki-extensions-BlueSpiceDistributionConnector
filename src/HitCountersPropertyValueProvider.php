<?php
namespace BlueSpice\DistributionConnector;

use BlueSpice\SMWConnector\PropertyValueProvider;
use SMWDataItem;
use \HitCounters\HitCounters;

class HitCountersPropertyValueProvider extends PropertyValueProvider {
	/**
	 *
	 * @return string
	 */
	public function getAliasMessageKey() {
		return "bs-distributionconnector-hitcounters-sesp-alias";
	}

	/**
	 *
	 * @return string
	 */
	public function getDescriptionMessageKey() {
		return "bs-distributionconnector-hitcounters-sesp-desc";
	}

	/**
	 *
	 * @return int
	 */
	public function getType() {
		return SMWDataItem::TYPE_NUMBER;
	}

	/**
	 *
	 * @return string
	 */
	public function getId() {
		return '_HITCOUNTERS';
	}

	/**
	 *
	 * @return string
	 */
	public function getLabel() {
		return "HitCounters";
	}

	/**
	 * @param \SESP\AppFactory $appFactory
	 * @param \SMW\DIProperty $property
	 * @param \SMW\SemanticData $semanticData
	 * @return null
	 */
	public function addAnnotation( $appFactory, $property, $semanticData ) {
		$intCount = (int)HitCounters::getCount( $semanticData->getSubject()->getTitle() );
		$semanticData->addPropertyObjectValue( $property, new \SMWDINumber( $intCount ) );

		return null;
	}
}
