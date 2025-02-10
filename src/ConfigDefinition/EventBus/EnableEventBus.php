<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\EventBus;

use BlueSpice\ConfigDefinition\ArraySetting;
use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\DistributionConnector\ISettingPaths;
use MediaWiki\HTMLForm\Field\HTMLSelectField;
use MediaWiki\Registration\ExtensionRegistry;

class EnableEventBus extends ArraySetting implements ISettingPaths, IOverwriteGlobal {
	private const EXTENSION_EVENT_BUS = 'EventBus';

	/**
	 * @return string[]
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SYSTEM . '/' . self::EXTENSION_EVENT_BUS,
			static::MAIN_PATH_EXTENSION . '/' . self::EXTENSION_EVENT_BUS . '/' . static::FEATURE_SYSTEM,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_CLOUD . '/' . self::EXTENSION_EVENT_BUS,
		];
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-distributionconnector-pref-eventbus-enableeventbus';
	}

	/**
	 *
	 * @return HTMLSelectField
	 */
	public function getHtmlFormField() {
		return new HTMLSelectField( $this->makeFormFieldParams() );
	}

	/**
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			$this->context->msg(
				'bs-distributionconnector-pref-eventbus-enableeventbus-key-all'
			)->text() => "TYPE_ALL",
			$this->context->msg(
				'bs-distributionconnector-pref-eventbus-enableeventbus-key-none'
			)->text() => "TYPE_NONE" ];
	}

	/**
	 *
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-distributionconnector-pref-eventbus-enableeventbus-help';
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( self::EXTENSION_EVENT_BUS );
	}

	/**
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return "wgEnableEventBus";
	}
}
