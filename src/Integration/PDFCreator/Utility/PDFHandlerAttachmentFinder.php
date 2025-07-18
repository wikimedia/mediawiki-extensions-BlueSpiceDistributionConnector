<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Utility;

use DOMDocument;
use DOMElement;
use DOMXPath;
use MediaWiki\Config\Config;
use MediaWiki\Extension\PDFCreator\Utility\AttachmentFinder;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWiki\Utils\UrlUtils;
use RepoGroup;

class PDFHandlerAttachmentFinder extends AttachmentFinder {

	/** @var UrlUtils */
	private $urlUtils;

	/**
	 * @param TitleFactory $titleFactory
	 * @param Config $config
	 * @param RepoGroup $repoGroup
	 * @param UrlUtils $urlUtils
	 */
	public function __construct(
		TitleFactory $titleFactory, Config $config, RepoGroup $repoGroup, UrlUtils $urlUtils
	) {
		$this->titleFactory = $titleFactory;
		$this->config = $config;
		$this->repoGroup = $repoGroup;
		$this->urlUtils = $urlUtils;
	}

	/**
	 * @param DOMDocument $dom
	 * @return void
	 */
	protected function find( DOMDocument $dom ): void {
		$xpath = new DOMXPath( $dom );
		// PDFHandlerThumbFinder marked thumbs with class "pdfhandler-thumb"
		$images = $xpath->query(
			'//img[contains(@class, "pdfhandler-thumb")]',
			$dom
		);

		/** @var DOMElement */
		foreach ( $images as $image ) {
			$attachment = $image->parentNode;
			if ( $attachment instanceof DOMElement === false ) {
				continue;
			}

			if ( !$attachment->hasAttribute( 'href' ) ) {
				continue;
			}
			$href = $attachment->getAttribute( 'href' );

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

			$absPath = $file->getLocalRefPath();

			$attachment->setAttribute( 'href', $title->getLocalURL() );
			$attachment->setAttribute( 'class', 'media pdfhandler-media' );

			$filename = $file->getName();
			$filename = $this->uncollideFilenames( $filename, $absPath );
			$url = $attachment->getAttribute( 'href' );

			if ( !isset( $this->data[$filename] ) ) {
				$this->data[$filename] = [
					'src' => [ $url ],
					'absPath' => $absPath,
					'filename' => $filename,
				];
			} elseif ( $this->data[$filename]['absPath'] === $absPath ) {
				$urls = &$this->data[$filename]['src'];
				if ( !in_array( $url, $urls ) ) {
					$urls[] = $url;
				}
			}

			// Embedding attachments did not work if link has a img tag as child.
			// To solve this we add a new link in thumb caption.
			$newAttachment = $attachment->ownerDocument->createElement( 'a', "($filename)" );
			$newAttachment->setAttribute( 'class', 'media pdfhandler-media' );
			$newAttachment->setAttribute( 'href', $attachment->getAttribute( 'href' ) );

			$figure = $attachment->parentNode;

			if ( $figure instanceof DOMElement && $figure->hasChildNodes() ) {
				foreach ( $figure->childNodes as $childNode ) {
					if ( $childNode->nodeName !== 'figcaption' ) {
						continue;
					}

					if ( $childNode->nodeValue !== '' ) {
						$childNode->nodeValue .= " ";
					}

					$childNode->appendChild( $newAttachment );
				}

			}
		}
	}
}
