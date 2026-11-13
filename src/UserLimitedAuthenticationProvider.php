<?php

namespace BlueSpice\DistributionConnector;

use MediaWiki\Auth\AbstractPreAuthenticationProvider;
use MediaWiki\Auth\AuthenticationRequest;
use MediaWiki\User\User;
use StatusValue;

class UserLimitedAuthenticationProvider extends AbstractPreAuthenticationProvider {

	/**
	 * @param UserCounter $userCounter
	 */
	public function __construct( private readonly UserCounter $userCounter ) {
	}

	/**
	 * Determine whether an account creation may begin
	 *
	 * Called from AuthManager::beginAccountCreation()
	 *
	 * @note No need to test if the account exists, AuthManager checks that
	 * @param User $user User being created (not added to the database yet).
	 *   This may become a "UserValue" in the future, or User may be refactored
	 *   into such.
	 * @param User $creator User doing the creation. This may become a
	 *   "UserValue" in the future, or User may be refactored into such.
	 * @param AuthenticationRequest[] $reqs
	 * @return StatusValue
	 */
	public function testForAccountCreation( $user, $creator, array $reqs ): StatusValue {
		if ( !$this->allowUserCreation() ) {
			return StatusValue::newFatal( 'bs-pro-distribution-createaccount-fail-max-accounts-exceed' );
		}

		return StatusValue::newGood();
	}

	/**
	 * @param User $user
	 * @param bool|string $autocreate
	 * @param array $options
	 *
	 * @return StatusValue
	 */
	public function testUserForCreation( $user, $autocreate, array $options = [] ): StatusValue {
		if ( !$this->allowUserCreation() ) {
			return StatusValue::newFatal( 'bs-pro-distribution-createaccount-fail-max-accounts-exceed' );
		}

		return StatusValue::newGood();
	}

	/**
	 * @return bool
	 */
	private function allowUserCreation(): bool {
		$userLimit = $this->userCounter->getUserLimit();

		// Unlimited user
		if ( $userLimit === -1 ) {
			return true;
		}

		$currentNumOfUser = $this->userCounter->getCurrentNumberOfUser();
		if ( $currentNumOfUser < $userLimit ) {
			return true;
		}

		return false;
	}
}
