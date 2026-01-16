<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Processor;

use DOMNodeList;
use DOMXPath;
use MediaWiki\Extension\PDFCreator\IProcessor;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;

class LingoProcessor implements IProcessor {

	/**
	 * @param ExportPage[] &$pages
	 * @param array &$images
	 * @param array &$attachments
	 * @param ExportContext $context
	 * @param string $module
	 * @param array $params
	 * @return void
	 */
	public function execute( array &$pages, array &$images, array &$attachments,
		ExportContext $context, string $module = '', $params = []
	): void {
		foreach ( $pages as $page ) {
			$dom = $page->getDomDocument();
			$xpath = new DOMXPath( $dom );
			$lingoLinkNodes = $xpath->query(
				"//a[contains(@class, 'mw-lingo-term')]",
				$dom
			);
			if ( $lingoLinkNodes instanceof DOMNodeList ) {
				$this->removeLingoLinks( $lingoLinkNodes );
			}
		}
	}

	/**
	 * @param DOMNodeList $lingoLinkNodes
	 * @return void
	 */
	private function removeLingoLinks( DOMNodeList $lingoLinkNodes ): void {
		foreach ( $lingoLinkNodes as $link ) {
			$span = $link->ownerDocument->createElement( 'span' );
			foreach ( $link->childNodes as $linkChildNode ) {
				$spanChildNode = $link->ownerDocument->importNode( $linkChildNode, true );
				$span->appendChild( $spanChildNode );
			}
			$link->parentNode->replaceChild( $span, $link );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getPosition(): int {
		return 10;
	}
}
