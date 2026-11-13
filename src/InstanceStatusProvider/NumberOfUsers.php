<?php

namespace BlueSpice\DistributionConnector\InstanceStatusProvider;

use BlueSpice\DistributionConnector\UserCounter;
use BlueSpice\InstanceStatus\IApiStatusProvider;
use BlueSpice\InstanceStatus\IStatusProvider;
use MediaWiki\Message\Message;

class NumberOfUsers implements IStatusProvider, IApiStatusProvider {
	/** @var UserCounter */
	private $counter;

	/**
	 * @param UserCounter $counter
	 */
	public function __construct( UserCounter $counter ) {
		$this->counter = $counter;
	}

	/**
	 * @return string
	 */
	public function getLabel(): string {
		return Message::newFromKey( 'bs-pro-distribution-instance-status-user-quota-label' )->text();
	}

	/**
	 * @return string
	 */
	public function getValue(): string {
		return $this->counter->getSentenceHtml();
	}

	/**
	 * @return string
	 */
	public function getIcon(): string {
		return 'userAvatar';
	}

	/**
	 * @return int
	 */
	public function getPriority(): int {
		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function getKeyForApi(): string {
		return 'bs-usercount';
	}

	/**
	 * @inheritDoc
	 */
	public function getValueForApi() {
		return $this->counter->getCurrentNumberOfUser();
	}
}
