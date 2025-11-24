<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Utility;

use DOMDocument;
use DOMElement;
use DOMXPath;
use MediaWiki\Extension\PDFCreator\Utility\AttachmentFinder;
use MediaWiki\Title\Title;

class PDFHandlerAttachmentFinder extends AttachmentFinder {

	/**
	 * @param DOMDocument $dom
	 * @return void
	 */
	protected function find( DOMDocument $dom ): void {
		$xpath = new DOMXPath( $dom );
		// PDFHandlerThumbFinder marked thumbs with class "pdfhandler-thumb"
		$attachments = $xpath->query(
			'//a[contains(@class, "pdfhandler-media")]',
			$dom
		);

		/** @var DOMElement */
		foreach ( $attachments as $attachment ) {
			if ( $attachment instanceof DOMElement === false ) {
				continue;
			}

			if ( !$attachment->hasAttribute( 'data-prefixeddbkey' ) ) {
				continue;
			}
			$prefixedDBKey = $attachment->getAttribute( 'data-prefixeddbkey' );
			$title = $this->titleFactory->newFromDBkey( $prefixedDBKey );

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

			$filename = $title->getPrefixedText();
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

			$attachment->setAttribute( 'href', "attachments/{$filename}" );
			$attachment->setAttribute( 'data-fs-embed-file', 'true' );
		}
	}
}
