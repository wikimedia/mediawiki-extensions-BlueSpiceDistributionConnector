<?php

namespace BlueSpice\DistributionConnector;

use Exception;
use MediaWiki\Extension\Notifications\EventFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Registration\ExtensionRegistry;
use MWStake\MediaWiki\Component\Events\NotificationEvent;
use MWStake\MediaWiki\Component\Events\Notifier;

class EchoEvent {

	/** @var array */
	private $compatMapping = [];

	/** @var Notifier */
	private $notifier;

	/** @var EventFactory */
	private $eventFactory;

	/**
	 * Replace EchoEvent::create
	 *
	 * @param array $info
	 * @return void
	 * @throws Exception
	 */
	public static function create( $info = [] ) {
		if ( !MediaWikiServices::getInstance()->hasService( 'MWStake.Notifier' ) ) {
			return;
		}
		if ( !isset( $info['type'] ) ) {
			return;
		}

		$instance = new static();
		$instance->maybeTriggerReplacement( $info );
	}

	public function __construct() {
		$this->compatMapping = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDistributionConnectorEchoEventsCompatibilityMapping'
		);
		$this->notifier = MediaWikiServices::getInstance()->getService( 'MWStake.Notifier' );
		$this->eventFactory = MediaWikiServices::getInstance()->getService( 'Notifications.EventFactory' );
	}

	/**
	 * @param array $info
	 * @return void
	 * @throws Exception
	 */
	public function maybeTriggerReplacement( array $info ) {
		if ( !isset( $this->compatMapping[$info['type']] ) ) {
			return;
		}

		$replacement = $this->compatMapping[$info['type']];
		$event = $this->getReplacementEvent( $info, $replacement );

		if ( $event ) {
			try {
				$this->notifier->emit( $replacement );
			} catch ( Exception $e ) {
				// Do nothing
			}

		}
	}

	/**
	 * @param array $info
	 * @param string $eventKey
	 * @return NotificationEvent|null
	 */
	private function getReplacementEvent( array $info, string $eventKey ): ?NotificationEvent {
		try {
			return $this->eventFactory->create( $eventKey, $info );
		} catch ( Exception $e ) {
			return null;
		}
	}
}
