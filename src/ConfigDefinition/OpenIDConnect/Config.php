<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition\OpenIDConnect;

use BlueSpice\ConfigDefinition\ArraySetting;
use BlueSpice\ConfigDefinition\IOverwriteGlobal;
use BlueSpice\DistributionConnector\ISettingPaths;
use BlueSpice\Html\FormField\KeyObjectField;
use BlueSpice\Html\OOUI\KeyObjectInputWidget;
use ExtensionRegistry;

class Config extends ArraySetting implements ISettingPaths, IOverwriteGlobal {

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
		return 'bs-distributionconnector-pref-openidconnect-config';
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
				'bs-distributionconnector-pref-openidconnect-config-key'
			)->text(),
			'valueLabel' => '',
			'objectConfiguration' => [
				'clientID' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => true,
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-clientid'
					)->text(),
				],
				'clientsecret' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => true,
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-clientsecret'
					)->text(),
				],
				'name' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-name'
					)->text(),
				],
				'icon' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-icon'
					)->text(),
				],
				'proxy' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-proxy'
					)->text(),
				],
				'scope' => [
					'type' => KeyObjectInputWidget::TYPE_JSON,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-scope'
					)->text(),
				],
				'preferred_username' => [
					'type' => KeyObjectInputWidget::TYPE_TEXT,
					'widget' => [
						'required' => false
					],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-preferredusername'
					)->text(),
				],
				'authparam' => [
					'type' => KeyObjectInputWidget::TYPE_JSON,
					'widget' => [ 'rows' => 1 ],
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-authparam'
					)->text(),
				],
				'verifyHost' => [
					'type' => KeyObjectInputWidget::TYPE_BOOL,
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-verifyhost'
					)->text(),
				],
				'verifyPeer' => [
					'type' => KeyObjectInputWidget::TYPE_BOOL,
					'label' => $this->context->msg(
						'bs-distributionconnector-pref-openidconnect-config-verifypeer'
					)->text(),
				],
			]
		] );
	}

	/**
	 *
	 * @return string
	 */
	public function getGlobalName() {
		return "wgOpenIDConnect_Config";
	}

}
