<?php

namespace BlueSpice\DistributionConnector\Hook\ChameleonSkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\ChameleonSkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class ModifySidebar extends ChameleonSkinTemplateOutputPageBeforeExec {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		$duplicator = \SpecialPage::getTitleFor( 'Duplicator' );

		$isAllowed = $this->getServices()->getPermissionManager()->userHasRight(
			$this->skin->getUser(),
			'duplicate'
		);
		if ( !$isAllowed ) {
			return true;
		}
		if ( !$duplicator->isKnown() || !$this->skin->getTitle()->isContentPage() ) {
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
