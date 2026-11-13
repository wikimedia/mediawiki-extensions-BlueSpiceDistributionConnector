<?php

namespace BlueSpice\DistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;
use BlueSpice\DistributionConnector\EditionProvider;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;

class SubscriptionKey extends ConfigDefinition\StringSetting {
	/** @var EditionProvider */
	private $editionProvider;

	/**
	 * @inheritDoc
	 */
	public static function getInstance( $context, $config, $name ) {
		$editionProvider = MediaWikiServices::getInstance()->getService( 'BlueSpiceEditionProvider' );
		return new static( $context, $config, $name, $editionProvider );
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string $name
	 * @param EditionProvider $editionProvider
	 */
	public function __construct( $context, $config, $name, EditionProvider $editionProvider ) {
		parent::__construct( $context, $config, $name );
		$this->editionProvider = $editionProvider;
	}

	/**
	 * @return string[]
	 */
	public function getPaths() {
		return [
				static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SYSTEM . '/BlueSpiceDistributionConnector',
				static::MAIN_PATH_EXTENSION . '/BlueSpiceDistributionConnector/' . static::FEATURE_SYSTEM,
				static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_PRO . '/BlueSpiceDistributionConnector',
			];
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-config-license-key";
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return !$this->editionProvider->checkIsSubscriptionKeyConfigurable();
	}
}
