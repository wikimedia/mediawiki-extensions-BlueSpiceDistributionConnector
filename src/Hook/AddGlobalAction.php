<?php

namespace BlueSpice\DistributionConnector\Hook;

use ExtensionRegistry;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\RestrictedTextLink;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;
use RawMessage;

class AddGlobalAction implements MWStakeCommonUIRegisterSkinSlotComponents {

	/** @var SpecialPageFactory */
	private $specialPageFactory;

	/**
	 * @param SpecialPageFactory $specialPageFactory
	 */
	public function __construct( SpecialPageFactory $specialPageFactory ) {
		$this->specialPageFactory = $specialPageFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ): void {
		// If ReplaceText is not available, do not register the link
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Replace Text' ) ) {
			return;
		}

		$special = $this->specialPageFactory->getPage( 'ReplaceText' );

		// Check if the user has the necessary permissions to access the special page
		// If not, do not register the link
		try {
			$special->checkPermissions();
		} catch ( \PermissionsError $e ) {
			return;
		}

		$registry->register(
			'GlobalActionsEditing',
			[
				'special-replacetext' => [
					'factory' => static function () use ( $special ) {
						return new RestrictedTextLink( [
							'id' => 'special-replacetext',
							'href' => $special->getPageTitle()->getLocalURL(),
							'text' => new RawMessage( $special->getDescription() ),
							'title' => new RawMessage( $special->getDescription() ),
							'aria-label' => new RawMessage( $special->getDescription() ),
							'permissions' => [ 'edit' ]
						] );
					}
				]
			]
		);
	}
}
