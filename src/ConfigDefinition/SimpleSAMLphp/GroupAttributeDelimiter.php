<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\SimpleSAMLphp;

use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\ConfigDefinition\StringSetting;
use BlueSpice\DistributionConnector\ISettingPaths;
use ExtensionRegistry;

class GroupAttributeDelimiter extends StringSetting implements ISettingPaths, IOverwriteGlobal {

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

		// As ConfigManager can not "enable/disable" certain configs we must
		// fall back to the default in Extension:SimpleSAMLphp manually
		if ( empty( $returnValue ) ) {
			$returnValue = null;
			if ( $this->config->has( 'SimpleSAMLphp_GroupAttributeDelimiter' ) ) {
				$localSettingsValue = $this->config->get(
					'SimpleSAMLphp_GroupAttributeDelimiter'
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
		return 'bs-distributionconnector-pref-simplesamlphp-groupattributedelimiter';
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
		return "wgSimpleSAMLphp_GroupAttributeDelimiter";
	}
}
