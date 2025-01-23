<?php

namespace BlueSpice\DistributionConnector\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\User\User;

abstract class DeleteAccount extends Hook {
	/**
	 *
	 * @var User
	 */
	protected $oldUser = null;

	/**
	 *
	 * @param User &$oldUser
	 * @return bool
	 */
	public static function callback( &$oldUser ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$oldUser
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param User &$oldUser
	 */
	public function __construct( $context, $config, &$oldUser ) {
		parent::__construct( $context, $config );

		$this->oldUser = &$oldUser;
	}
}
