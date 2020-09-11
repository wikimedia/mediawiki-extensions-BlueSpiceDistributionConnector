<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\OpenIDConnect;

use BlueSpice\ConfigDefinition\BooleanSetting;
use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\DistributionConnector\ISettingPaths;
use ExtensionRegistry;

class UseEmailNameAsUserName extends BooleanSetting implements ISettingPaths, IOverwriteGlobal {

	/**
	 *
	 * @return string[]
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_AUTHENTICATION . '/OpenIDConnect',
			static::MAIN_PATH_EXTENSION . '/OpenIDConnect/' . static::FEATURE_AUTHENTICATION,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_CLOUD . '/OpenIDConnect',
		];
	}

	/**
	 *
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-distributionconnector-pref-openidconnect-useemailasusername';
	}

	/**
	 *
	 * @return bool
	 */
	public function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( 'OpenID Connect' );
	}

	/**
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return "wgOpenIDConnect_UseEmailNameAsUserName";
	}
}
