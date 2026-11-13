<?php

namespace BlueSpice\DistributionConnector\JSConfigVariable;

use BlueSpice\JSConfigVariable;
use MediaWiki\MediaWikiServices;

class UserLimit extends JSConfigVariable {

	/**
	 * @inheritDoc
	 */
	public function getValue() {
		$userCounter = MediaWikiServices::getInstance()->getService( 'BlueSpiceUserCounter' );
		return [
			'sentence' => $userCounter->getSentence(),
			'percent' => $userCounter->getRatio(),
		];
	}
}
