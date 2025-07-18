<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Utility;

use DOMDocument;
use DOMElement;
use DOMXPath;
use MediaWiki\Config\Config;
use MediaWiki\Extension\PDFCreator\Utility\WikiFileResource;

class PDFHandlerThumbFinder {

	/** @var Config */
	protected $config;

	/** @var array */
	protected $data = [];

	/** @var array */
	protected $filenames = [];

	/**
	 * @param Config $config
	 */
	public function __construct( Config $config	) {
		$this->config = $config;
	}

	/**
	 * @param array $pages
	 * @param array $resources
	 * @return array
	 */
	public function execute( array $pages, array $resources = [] ): array {
		$files = [];

		foreach ( $resources as $filename => $resourcePath ) {
			$this->data[$filename] = [
				'src' => [],
				'absPath' => $resourcePath,
				'filename' => $filename
			];
		}

		foreach ( $pages as $page ) {
			$dom = $page->getDOMDocument();
			$this->find( $dom );
		}

		foreach ( $this->data as $data ) {
			$files[] = new WikiFileResource(
				$data['src'],
				$data['absPath'],
				$data['filename']
			);
		}

		return $files;
	}

	/**
	 * @param DOMDocument $dom
	 * @return void
	 */
	protected function find( DOMDocument $dom ): void {
		$xpath = new DOMXPath( $dom );
		$figures = $xpath->query(
			'//figure',
			$dom
		);

		/** @var FileResolver */
		$fileResolver = $this->getFileResolver();

		foreach ( $figures as $figure ) {
			if ( $figure instanceof DOMElement === false ) {
				continue;
			}
			$images = $figure->getElementsByTagName( 'img' );
			/** @var DOMElement */
			foreach ( $images as $image ) {
				if ( !$image->hasAttribute( 'src' ) ) {
					continue;
				}

				$fileData = $fileResolver->execute( $image );
				if ( !$fileData ) {
					continue;
				}

				$filename = $fileData['filename'];
				$absPath = $fileData['absPath'];

				$filename = $this->uncollideFilenames( $filename, $absPath );
				$url = $image->getAttribute( 'src' );

				if ( !isset( $this->data[$filename] ) ) {
					$this->data[$filename] = [
						'src' => [ $url ],
						'absPath' => $absPath,
						'filename' => str_replace( ':', '_', $filename )
					];
				} elseif ( $this->data[$filename]['absPath'] === $absPath ) {
					$urls = &$this->data[$filename]['src'];
					if ( !in_array( $url, $urls ) ) {
						$urls[] = $url;
					}
				}

				$classes = $image->getAttribute( 'class' );
				$classes .= ' pdfhandler-thumb';
				$image->setAttribute( 'class', trim( $classes ) );

				if ( $image->hasAttribute( 'srcset' ) ) {
					$image->setAttribute( 'srcset', '' );
				}
			}
		}
	}

	/**
	 * @return void
	 */
	protected function getFileResolver() {
		return new PDFHandlerThumbFileResolver( $this->config );
	}

	/**
	 * @param string $filename
	 * @param array $absPath
	 * @return string
	 */
	protected function uncollideFilenames( string $filename, string $absPath ): string {
		if ( !isset( $this->data[$filename] ) ) {
			return $filename;
		}

		if ( $this->data[$filename]['absPath'] === $absPath ) {
			return $filename;
		}

		$extPos = strrpos( $filename, '.' );
		$ext = substr( $filename, $extPos + 1 );
		$name = substr( $filename, 0, $extPos );

		$uncollide = 1;
		$newFilename = $filename;

		while ( isset( $this->data[$newFilename] ) && $this->data[$newFilename]['absPath'] !== $absPath ) {
			$uncollideStr = (string)$uncollide;
			$newFilename = "{$name}_{$uncollideStr}.{$ext}";
			$uncollide++;
		}
		return $newFilename;
	}
}
