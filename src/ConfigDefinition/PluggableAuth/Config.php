<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\PluggableAuth;

use BlueSpice\ConfigDefinition\ArraySetting;
use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\DistributionConnector\ISettingPaths;
use BlueSpice\Html\FormField\KeyObjectField;
use BlueSpice\Html\OOUI\KeyObjectInputWidget;
use MediaWiki\Registration\ExtensionRegistry;

class Config extends ArraySetting implements ISettingPaths, IOverwriteGlobal {

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
		return 'bs-distributionconnector-pref-pluggableauth-config';
	}

	/**
	 * @return KeyObjectField
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
				'bs-distributionconnector-pref-pluggableauth-config-button-label'
			)->text(),
			'objectConfiguration' => [
				'plugin' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => true,
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-pluggableauth-config-plugin'
					)->text()
				],
				'data' => [
					'type' => KeyObjectInputWidget::TYPE_JSON,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-pluggableauth-config-data-object'
					)->text()
				],
				'groupsyncs' => [
					'type' => KeyObjectInputWidget::TYPE_JSON,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-pluggableauth-config-groupsync-object'
					)->text()
				]
			]
		] );
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
		return 'wgPluggableAuth_Config';
	}
}
