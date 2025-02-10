<?php

namespace BlueSpice\DistributionConnector\Hook\SkinTemplateNavigation;

use BlueSpice\Discovery\Skin as DiscoverySkin;
use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\Title\Title;

class AddNewPageLink implements SkinTemplateNavigation__UniversalHook {

	/** @var PermissionManager */
	private $permissionManager = null;

	/**
	 *
	 * @param PermissionManager $permissionManager
	 */
	public function __construct( PermissionManager $permissionManager ) {
		$this->permissionManager = $permissionManager;
	}

	/**
	 * // phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
	 * @inheritDoc
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		$skin = $sktemplate->getSkin();
		if ( !is_a( $skin, DiscoverySkin::class, true ) ||
			!ExtensionRegistry::getInstance()->isLoaded( 'StandardDialogs' ) ) {
			return;
		}
		$user = $skin->getUser();
		$title = $skin->getRelevantTitle();
		if ( $title instanceof Title === false ) {
			return;
		}

		$userCanCreatePages = $this->permissionManager->userHasRight( $user, 'createpage' );
		if ( !$userCanCreatePages ) {
			return;
		}
		$links['namespaces']['new-page'] = [
			'text' => $sktemplate->msg( 'standarddialogs-create-button-new-page-text' ),
			'title' => $sktemplate->msg( 'standarddialogs-create-button-new-page-title' ),
			'href' => '',
			'class' => 'new-page'
		];
		$sktemplate->getOutput()->addModules( 'ext.standardDialogs' );
	}
}
