<?php

namespace BlueSpice\DistributionConnector;

class EditionProvider {

	/**
	 * @return string|null if no edition info can be found
	 */
	public function getEdition(): ?string {
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
