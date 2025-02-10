<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\EventBus;

use BlueSpice\ConfigDefinition\ArraySetting;
use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\DistributionConnector\ISettingPaths;
use BlueSpice\Html\FormField\KeyObjectField;
use BlueSpice\Html\OOUI\KeyObjectInputWidget;
use MediaWiki\Registration\ExtensionRegistry;

class EventServices extends ArraySetting implements ISettingPaths, IOverwriteGlobal {
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
		return 'bs-distributionconnector-pref-eventbus-eventservices';
	}

	/**
	 * @return KeyValueField
	 */
	public function getHtmlFormField() {
		return new KeyObjectField( $this->makeFormFieldParams() );
	}

	/**
	 *
	 * @return array
	 */
	protected function makeFormFieldParams() {
		return array_merge( parent::makeFormFieldParams(), [
			'allowAdditions' => true,
			'labelsOnlyOnFirst' => true,
			'keyLabel' => $this->context->msg(
				'bs-distributionconnector-pref-eventbus-eventservices-key'
			)->text(),
			'valueLabel' => 'Test',
			'objectConfiguration' => [
				'url' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => true,
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-eventbus-eventservices-subkey-url'
					)->text(),
				],
				'timeout' => [
					'type' => KeyObjectInputWidget::TYPE_NUMBER,
					'widget' => [
						'required' => false,
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-eventbus-eventservices-subkey-timeout'
					)->text(),
				],
			]
		] );
	}

	/**
	 *
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-distributionconnector-pref-eventbus-eventservices-help';
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
		return "wgEventServices";
	}
}
