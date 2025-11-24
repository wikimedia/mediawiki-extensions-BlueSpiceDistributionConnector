<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Utility;

use DOMDocument;
use DOMElement;
use DOMXPath;

class PDFHandlerThumbFinder extends PDFHandlerImageFinder {

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

		$this->processFigures( $figures );
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

		$link = $element->ownerDocument->createElement( 'a', "({$filename})" );
		$link->setAttribute( 'class', 'pdfhandler-media' );
		$link->setAttribute( 'href', $url );
		$link->setAttribute( 'data-prefixeddbkey', $prefixedDBKey );

		$container->appendChild( $link );

		if ( $element instanceof DOMElement && $element->hasChildNodes() ) {
			foreach ( $element->childNodes as $childNode ) {
				if ( $childNode->nodeName !== 'figcaption' ) {
					continue;
				}

				if ( $childNode->nodeValue !== '' ) {
					$childNode->nodeValue .= " ";
				}

				$childNode->appendChild( $container );
			}
		}
	}
}
