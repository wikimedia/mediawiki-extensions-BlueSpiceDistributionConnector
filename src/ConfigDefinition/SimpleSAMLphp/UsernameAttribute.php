<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\SimpleSAMLphp;

use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\ConfigDefinition\StringSetting;
use BlueSpice\DistributionConnector\ISettingPaths;
use ExtensionRegistry;

class UsernameAttribute extends StringSetting implements ISettingPaths, IOverwriteGlobal {

	/**
	 *
	 * @return string[]
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_AUTHENTICATION . '/SAML',
			static::MAIN_PATH_EXTENSION . '/SAML/' . static::FEATURE_AUTHENTICATION,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_CLOUD . '/SAML',
		];
	}

	/**
	 *
	 * @return mixed
	 */
	public function getValue() {
		$returnValue = parent::getValue();

		// Initially the value is an empty array for some reason
		if ( empty( $returnValue ) ) {
			$returnValue = '';
			if ( $this->config->has( 'SimpleSAMLphp_UsernameAttribute' ) ) {
				$localSettingsValue = $this->config->get(
					'SimpleSAMLphp_UsernameAttribute'
				);
			}
			if ( !empty( $localSettingsValue ) ) {
				$returnValue = $localSettingsValue;
			}
		}

		return $returnValue;
	}

	/**
	 *
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-distributionconnector-pref-simplesamlphp-usernameattribute';
	}

	/**
	 *
	 * @return bool
	 */
	public function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( 'SimpleSAMLphp' );
	}

	/**
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return "wgSimpleSAMLphp_UsernameAttribute";
	}
}
