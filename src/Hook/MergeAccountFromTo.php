<?php

namespace BlueSpice\DistributionConnector\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\User\User;

abstract class MergeAccountFromTo extends Hook {
	/**
	 *
	 * @var User
	 */
	protected $oldUser = null;

	/**
	 *
	 * @var User
	 */
	protected $newUser = null;

	/**
	 *
	 * @param User &$oldUser
	 * @param User &$newUser
	 * @return bool
	 */
	public static function callback( &$oldUser, &$newUser ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$oldUser,
			$newUser
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param User &$oldUser
	 * @param User &$newUser
	 */
	public function __construct( $context, $config, &$oldUser, &$newUser ) {
		parent::__construct( $context, $config );

		$this->oldUser = &$oldUser;
		$this->newUser = &$newUser;
	}
}
