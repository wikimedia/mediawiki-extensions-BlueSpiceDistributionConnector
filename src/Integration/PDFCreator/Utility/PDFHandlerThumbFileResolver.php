<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Utility;

use DOMElement;
use MediaWiki\Config\Config;

class PDFHandlerThumbFileResolver {

	/** @var Config */
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct( Config $config	) {
		$this->config = $config;
	}

	/**
	 * @param DOMElement $element
	 * @param string $attrSrc
	 * @return array|null
	 */
	public function execute( DOMElement $element, string $attrSrc = 'src' ): ?array {
		$src = $element->getAttribute( 'src' );
		if ( strpos( $src, '?' ) ) {
			$src = substr( $src, 0, strpos( $src, '?' ) );
		}
		$srcUrl = $this->stripUrl( $src );

		$isThumb = $this->isThumb( $srcUrl );
		if ( !$isThumb ) {
			return null;
		}

		$srcset = $element->getAttribute( 'srcset' );
		$srcsetUrls = $this->getSrcsetUrls( $srcset );

		// get thumb filename for srcset
		$srcsetFilenameToUrlMap = [];
		for ( $index = 0; $index < count( $srcsetUrls ); $index++ ) {
			$filenamePos = strrpos( $srcsetUrls[$index], '/' );
			$filename = substr( $srcsetUrls[$index], $filenamePos + 1 );

			$srcsetFilenameToUrlMap[$filename] = $srcsetUrls[$index];
		}

		// get thumb filename
		$thumbFilenamePos = strrpos( $srcUrl, '/' );
		$thumbFilename = substr( $srcUrl, $thumbFilenamePos + 1 );
		$bestFilename = $this->findBestQuallityFilename( $thumbFilename, $srcsetFilenameToUrlMap );
		if ( $bestFilename !== $thumbFilename ) {
			$thumbFilename = $bestFilename;
			$srcUrl = $srcsetFilenameToUrlMap[$bestFilename];
		}

		// get original filename
		$origFilenamePath = substr( $srcUrl, 0, $thumbFilenamePos );
		$origFilenamePos = strrpos( $origFilenamePath, '/' );
		$origFilename = substr( $origFilenamePath, $origFilenamePos + 1 );

		// get file extensions
		$thumExtPos = strrpos( $thumbFilename, '.' );
		$thumbExt = substr( $thumbFilename, $thumExtPos + 1 );
		$origExtPos = strrpos( $origFilename, '.' );
		$origExt = substr( $origFilename, $origExtPos + 1 );

		if ( $origExt !== $thumbExt && strtolower( $origExt ) === 'pdf' ) {
			$path = preg_replace_callback(
				'#.*(thumb.*)#',
				static function ( $matches ) {
					return $matches[ 1 ];
				}, $srcUrl
			);

			return [
				'filename' => $thumbFilename,
				'absPath' => $this->config->get( 'UploadDirectory' ) . "/{$path}"
			];
		}

		return null;
	}

	/**
	 * @param string $localPath
	 * @return bool
	 */
	public function isThumb( string $localPath ): bool {
		$localPath = trim( $localPath, '/' );
		if ( ( strpos( $localPath, 'thumb' ) === 0 )
			|| ( strpos( $localPath, 'images/thumb' ) === 0 )
			|| ( strpos( $localPath, 'nsfr_img_auth.php/thumb' ) === 0 ) ) {
			return true;
		}
		return false;
	}

	/**
	 * @param string $srcset
	 * @return array
	 */
	private function getSrcsetUrls( string $srcset ): array {
		if ( $srcset === '' ) {
			return [];
		}

		$srcsets = explode( ',', $srcset );
		$srcsets = array_map( function ( $item ) {
			$item = trim( $item );
				$item = substr( $item, 0, strpos( $item, ' ' ) );
				return $this->stripUrl( $item );
		}, $srcsets
		);

		return $srcsets;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	private function stripUrl( string $url ): string {
		$pathsForRegex = [
			$this->config->get( 'Server' ),
			$this->config->get( 'ThumbnailScriptPath' ) . "?f=",
			$this->config->get( 'UploadPath' ),
			$this->config->get( 'ScriptPath' )
		];

		$url = urldecode( $url );

		// Extracting the filename
		foreach ( $pathsForRegex as $path ) {
			$url = preg_replace( "#" . preg_quote( $path, "#" ) . "#", '', $url );
			$url = preg_replace( '/(&.*)/', '', $url );
		}

		return $url;
	}

	/**
	 * @param string $filename
	 * @param array $srcset
	 * @return string
	 */
	private function findBestQuallityFilename( string $filename, array $srcset ): string {
		$keys = array_keys( $srcset );

		for ( $index = 0; $index < count( $srcset ); $index++ ) {
			$filenameSize = $this->getThumbSizeFromFilename( $filename );

			$key = $keys[$index];
			$item = $srcset[$key];
			$pos = strrpos( $item, '/' );
			$itemFilename = substr( $item, $pos + 1 );
			$itemSize = $this->getThumbSizeFromFilename( $itemFilename );

			if ( $itemSize > $filenameSize ) {
				$filename = $itemFilename;
			}
		}

		return $filename;
	}

	/**
	 * @param string $filename
	 * @return int
	 */
	private function getThumbSizeFromFilename( string $filename ): int {
		$result = preg_replace_callback(
			'#page.*?(\d*)px.*#',
			static function ( $matches ) {
				return $matches[1];
			},
			$filename
		);
		if ( $result === $filename ) {
			return 0;
		}
		return (int)$result;
	}
}
