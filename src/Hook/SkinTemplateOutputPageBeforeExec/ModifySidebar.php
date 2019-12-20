<?php

namespace BlueSpice\DistributionConnector\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class ModifySidebar extends SkinTemplateOutputPageBeforeExec {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		$duplicator = \SpecialPage::getTitleFor( 'Duplicator' );

		if ( !$duplicator->isKnown()
			|| !$this->skin->getTitle()->isContentPage()
			|| !$this->skin->getUser()->isAllowed( 'duplicate' ) ) {

			return true;
		}

		return false;
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->appendSkinDataArray( SkinData::TOOLBOX_BLACKLIST, 'duplicator' );
		$this->mergeSkinDataArray(
			SkinData::EDIT_MENU,
			[
				'duplicator' => [
					// Taken from original Extension:Duplicator codebase
					'text' => $this->skin->msg( 'duplicator-toolbox' ),
					'href' => $this->skin->makeSpecialUrl(
						'Duplicator',
						"source=" . wfUrlEncode( "{$this->skin->thispage}" )
						)
				]
			]
		);

		return true;
	}
}
