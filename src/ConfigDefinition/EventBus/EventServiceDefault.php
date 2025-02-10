<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\EventBus;

use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\ConfigDefinition\StringSetting;
use BlueSpice\DistributionConnector\ISettingPaths;
use MediaWiki\Registration\ExtensionRegistry;

class EventServiceDefault extends StringSetting implements ISettingPaths, IOverwriteGlobal {
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
		return 'bs-distributionconnector-pref-eventbus-eventservicedefault';
	}

	/**
	 *
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-distributionconnector-pref-eventbus-eventservicedefault-help';
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
		return "wgEventServiceDefault";
	}
}
