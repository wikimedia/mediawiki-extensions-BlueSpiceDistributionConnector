<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\SimpleSAMLphp;

use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\ConfigDefinition\StringSetting;
use BlueSpice\DistributionConnector\ISettingPaths;
use ExtensionRegistry;

class RealNameAttribute extends StringSetting implements ISettingPaths, IOverwriteGlobal {

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
			if ( $this->config->has( 'SimpleSAMLphp_RealNameAttribute' ) ) {
				$localSettingsValue = $this->config->get(
					'SimpleSAMLphp_RealNameAttribute'
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
		return 'bs-distributionconnector-pref-simplesamlphp-realnameattribute';
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
		return "wgSimpleSAMLphp_RealNameAttribute";
	}
}
