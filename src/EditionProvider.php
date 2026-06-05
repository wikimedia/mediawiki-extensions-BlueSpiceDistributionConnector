<?php

namespace BlueSpice\DistributionConnector;

class EditionProvider {

	/**
	 * @var string|null
	 */
	protected ?string $edition = '<not-set>';

	/**
	 * @return string|null if no edition info can be found
	 */
	public function getEdition(): ?string {
		if ( $this->edition === '<not-set>' ) {
			$this->edition = $this->readOutEdition();
		}
		return $this->edition;
	}

	private function readOutEdition(): ?string {
		$edition = getenv( 'EDITION' );
		if ( $edition ) {
			return $edition;
		}
		$file = $GLOBALS['IP'] . '/BLUESPICE-EDITION';
		if ( !file_exists( $file ) ) {
			return null;
		}
		return strtolower( trim( file_get_contents( $file ) ) );
	}
}
