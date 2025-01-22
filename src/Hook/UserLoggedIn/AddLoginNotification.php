<?php

namespace BlueSpice\DistributionConnector\Hook\UserLoggedIn;

use BlueSpice\SimpleDeferredNotification;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MediaWiki\User\User;

class AddLoginNotification {

	/**
	 *
	 * @param User $user
	 * @return bool
	 */
	public static function onUserLoggedIn( $user ) {
		MediaWikiServices::getInstance()->getService( 'BSDeferredNotificationStack' )->push(
			new SimpleDeferredNotification(
				[
					'message' => Message::newFromKey(
						'bs-distributionconnector-notification-logged-in' ),
					'options' => [
						'type' => 'info'
					]
				]
			)
		);

		return true;
	}
}
