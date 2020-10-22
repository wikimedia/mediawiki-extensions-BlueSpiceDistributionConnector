<?php

namespace BlueSpice\DistributionConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;

class AddResources extends BeforePageDisplay {

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.distribution.styles' );
		$spFactory = $this->getServices()->getSpecialPageFactory();
		if ( !$spFactory->exists( 'Userlogin' ) ) {
			return true;
		}
		$userLogin = $spFactory->getPage( 'Userlogin' );
		if ( $this->out->getTitle()->equals( $userLogin->getPageTitle() ) ) {
			$this->out->addModules( 'ext.bluespice.distribution.ldap' );
		}
		return true;
	}

}
