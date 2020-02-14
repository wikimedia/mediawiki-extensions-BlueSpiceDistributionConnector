<?php

namespace BlueSpice\DistributionConnector\Hook\BeforePageDisplay;

use BlueSpice\Hook\BeforePageDisplay;
use SpecialPageFactory;

class AddResources extends BeforePageDisplay {

	protected function doProcess() {
		$this->out->addModules( 'ext.bluespice.distribution.styles' );
		$userLogin = SpecialPageFactory::getPage( 'UserLogin' );
		if ( $this->out->getTitle()->equals( $userLogin->getPageTitle() ) ) {
			$this->out->addModules( 'ext.bluespice.distribution.ldap' );
		}
	}

}
