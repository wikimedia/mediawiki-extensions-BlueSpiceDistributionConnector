<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\PluggableAuth;

use BlueSpice\ConfigDefinition\BooleanSetting;
use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\DistributionConnector\ISettingPaths;
use MediaWiki\Registration\ExtensionRegistry;

class EnableAutoLogin extends BooleanSetting implements ISettingPaths, IOverwriteGlobal {

	/**
	 * @inheritDoc
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_AUTHENTICATION . '/PluggableAuth',
			static::MAIN_PATH_EXTENSION . '/PluggableAuth/' . static::FEATURE_AUTHENTICATION,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/PluggableAuth',
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getLabelMessageKey() {
		return 'bs-distributionconnector-pref-pluggableauth-enableautologin';
	}

	/**
	 * @inheritDoc
	 */
	public function getHelpMessageKey() {
		return 'bs-distributionconnector-pref-pluggableauth-enableautologin-help';
	}

	/**
	 * @inheritDoc
	 */
	public function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( 'PluggableAuth' );
	}

	/**
	 * @inheritDoc
	 */
	public function getGlobalName() {
		return 'wgPluggableAuth_EnableAutoLogin';
	}
}
