<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use BlueSpice\DistributionConnector\UserCounter;
use MediaWiki\Hook\UnblockUserHook;
use MediaWiki\Output\Hook\BeforePageDisplayHook;

class ApplyUserQuota implements UnblockUserHook, BeforePageDisplayHook {

	/** @var UserCounter */
	private $userCounter;

	/**
	 * @param UserCounter $userCounter
	 */
	public function __construct( UserCounter $userCounter ) {
		$this->userCounter = $userCounter;
	}

	/**
	 * @inheritDoc
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		if ( $out->getTitle() && $out->getTitle()->isSpecial( 'UserManager' ) ) {
			$out->addModules( [ 'ext.bluespice.distribution.usermanager.userlimit' ] );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function onUnblockUser( $block, $user, &$reason ) {
		$userLimit = $this->userCounter->getUserLimit();

		if ( $userLimit === -1 ) {
			return true;
		}

		$currentNumOfUser = $this->userCounter->getCurrentNumberOfUser();
		if ( $currentNumOfUser < $userLimit ) {
			return true;
		}

		$reason = [ 'bs-pro-distribution-createaccount-fail-max-accounts-exceed' ];

		return false;
	}
}
