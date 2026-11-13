<?php

namespace BlueSpice\DistributionConnector;

class EditionProvider {

	/**
	 * Editions that are limited by the user limit of a subscription key
	 */
	private const SUBSCRIPTION_KEY_EDITIONS = [ 'pro', 'cloud', 'sky', 'sapphire' ];

	/**
	 * @var string|null
	 */
	protected ?string $edition = '<not-set>';

	/**
	 * @return string|null if no edition info can be found
	 */
	public function getEdition(): ?string {
		if ( $this->edition === '<not-set>' ) {
			// `INTERNAL_EDITION` is set by the container image, `EDITION` is the legacy variable
			$this->edition = $this->readOut( [ 'INTERNAL_EDITION', 'EDITION' ], 'BLUESPICE-EDITION' );
		}
		return $this->edition;
	}

	/**
	 * Check whether the edition requires a subscription key
	 *
	 * @return bool
	 */
	public function checkRequiresSubscriptionKey(): bool {
		$edition = $this->getEdition();
		if ( !$edition ) {
			return true;
		}
		return in_array( $edition, self::SUBSCRIPTION_KEY_EDITIONS );
	}

	/**
	 * @return bool
	 */
	public function checkIsSubscriptionKeyConfigurable(): bool {
		return $this->checkRequiresSubscriptionKey() && $this->getEdition() !== 'cloud';
	}

	/**
	 * Fixed user limit of the edition (e.g. BlueSpice Galaxy Glacier). It is set by `INTERNAL_USER_LIMIT`
	 * in `_bluespice/.env.infrastructure.<edition>` of the codebase and replaces the limit of a subscription key.
	 *
	 * @return int|null if the edition has no fixed user limit
	 */
	public function getEditionUserLimit(): ?int {
		$limit = trim( (string)getenv( 'INTERNAL_USER_LIMIT' ) );
		if ( !ctype_digit( $limit ) || (int)$limit === 0 ) {
			return null;
		}
		return (int)$limit;
	}

	/**
	 * @param string[] $envVars
	 * @param string $fileName
	 * @return string|null
	 */
	private function readOut( array $envVars, string $fileName ): ?string {
		foreach ( $envVars as $envVar ) {
			$value = getenv( $envVar );
			if ( is_string( $value ) && trim( $value ) !== '' ) {
				return strtolower( trim( $value ) );
			}
		}
		$file = $GLOBALS['IP'] . '/' . $fileName;
		if ( !file_exists( $file ) ) {
			return null;
		}
		return strtolower( trim( file_get_contents( $file ) ) );
	}
}
