<?php

namespace BlueSpice\DistributionConnector\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class ModifySidebar extends SkinTemplateOutputPageBeforeExec {

	protected function doProcess() {

		if ( \SpecialPageFactory::exists( 'Duplicator' ) ) {
			$this->appendSkinDataArray( SkinData::TOOLBOX_BLACKLIST, 'duplicator' );
			$this->mergeSkinDataArray( SkinData::EDIT_MENU, [
					'duplicator' => [
						//Taken from original Extension:Duplicator codebase
						'text' => $this->skin->msg( 'duplicator-toolbox' ),
						'href' => $this->skin->makeSpecialUrl( 'Duplicator', "source=" . wfUrlEncode( "{$this->skin->thispage}" ) )
				]
			] );
		}

		return true;
	}
}
