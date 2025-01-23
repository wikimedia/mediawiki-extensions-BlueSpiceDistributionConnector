<?php

namespace BlueSpice\DistributionConnector\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class UserMergeAccountFields extends Hook {
	/**
	 *
	 * @var array
	 */
	protected $updateFields = null;

	/**
	 *
	 * @param array &$updateFields
	 * @return bool
	 */
	public static function callback( &$updateFields ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$updateFields
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param array &$updateFields
	 */
	public function __construct( $context, $config, &$updateFields ) {
		parent::__construct( $context, $config );

		$this->updateFields = &$updateFields;
	}
}
