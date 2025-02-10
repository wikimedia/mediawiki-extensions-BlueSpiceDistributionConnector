<?php

namespace BlueSpice\DistributionConnector\Hook;

use BlueSpice\UserManager\Hook\BSUserManagerRegisterModules;
use MediaWiki\Registration\ExtensionRegistry;

class AddInviteLink implements BSUserManagerRegisterModules {

	/**
	 * @inheritDoc
	 */
	public function onBSUserManagerRegisterModules( &$modules, $user ): void {
		if ( !$this->isHidden() ) {
			$modules[] = "ext.bluespice.distribution.usermanager.invite";
		}
	}

	/**
	 *
	 * @return bool
	 */
	private function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( 'InviteSignup' );
	}

}
