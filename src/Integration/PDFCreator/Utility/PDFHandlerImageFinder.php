<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Utility;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use MediaWiki\Config\Config;
use MediaWiki\Extension\PDFCreator\Utility\WikiFileResource;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWiki\Utils\UrlUtils;
use RepoGroup;

class PDFHandlerImageFinder {

	/** @var Config */
	protected $config;

	/** @var array */
	protected $data = [];

	/** @var array */
	protected $filenames = [];

	/** @var string */
	protected $xpathQuery = '//span[@typeof="mw:File"]';

	/** @var TitleFactory */
	protected $titleFactory;

	/** @var UrlUtils */
	protected $urlUtils;

	/** @var RepoGroup */
	protected $repoGroup;

	/**
	 * @param Config $config
	 * @param TitleFactory $titleFactory
	 * @param UrlUtils $urlUtils
	 * @param RepoGroup $repoGroup
	 */
	public function __construct( Config $config, TitleFactory $titleFactory, UrlUtils $urlUtils, RepoGroup $repoGroup ) {
		$this->config = $config;
		$this->titleFactory = $titleFactory;
		$this->urlUtils = $urlUtils;
		$this->repoGroup = $repoGroup;
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
				'filename' => $filename,
				'known' => 'true'
			];
		}

		foreach ( $pages as $page ) {
			$dom = $page->getDOMDocument();
			$this->find( $dom );
		}

		foreach ( $this->data as $data ) {
			if ( isset( $data['known'] ) ) {
				continue;
			}
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
			'//span[@typeof="mw:File"]',
			$dom
		);

		$this->processFigures( $figures );
	}

	/**
	 * @param DOMNodeList $figures
	 */
	protected function processFigures( DOMNodeList $figures ) {
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

				$anchor = $image->parentNode;
				if ( $anchor instanceof DOMElement === false
					|| !$anchor->hasAttribute( 'href' )
				) {
					return;
				}

				$href = $anchor->getAttribute( 'href' );

				$expanded = $this->urlUtils->expand( $href );
				$expanded = urldecode( $expanded );

				$titleText = '';
				$parsedUrl = $this->urlUtils->parse( $expanded );
				if ( isset( $parsedUrl['query'] ) ) {
					$query = explode( '&', $parsedUrl['query'] );
					foreach ( $query as $param ) {
						if ( substr( trim( $param ), 0, strlen( 'title=' ) ) === 'title=' ) {
							$titleText = substr( trim( $param ), strlen( 'title=' ) );

							if ( str_contains( $titleText, '?' ) ) {
								$titleText = substr( $titleText, 0, strpos( $titleText, '?' ) );
							}
						}
					}
				}

				if ( $titleText === '' ) {
					$titleText = substr( $parsedUrl['path'], strrpos( $parsedUrl['path'], '/' ) + 1 );
				}

				$title = $this->titleFactory->newFromText( $titleText );
				if ( $title instanceof Title === false ) {
					continue;
				}

				$file = $this->repoGroup->findFile( $title );
				if ( !$file ) {
					continue;
				}

				$this->appendLink( $figure, $title->getFullUrl(), $file->getName(), $title->getPrefixedDBKey() );
			}
		}
	}

	/**
	 * @param DOMElement $element
	 * @param string $url
	 * @param string $filename
	 * @param string $prefixedDBKey
	 * @return void
	 */
	protected function appendLink( DOMElement $element, string $url, string $filename, string $prefixedDBKey ): void {
		// Embedding attachments did not work if link has a img tag as child.
		// To solve this we add a new link in thumb caption.
		$container = $element->ownerDocument->createElement( 'div' );
		$container->setAttribute( 'class', 'pdfhander-caption' );

		$link = $container->ownerDocument->createElement( 'a', "({$filename})" );
		$link->setAttribute( 'class', 'pdfhandler-media' );
		$link->setAttribute( 'href', $url );
		$link->setAttribute( 'data-prefixeddbkey', $prefixedDBKey );

		$container->appendChild( $link );
		$element->appendChild( $container );
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
