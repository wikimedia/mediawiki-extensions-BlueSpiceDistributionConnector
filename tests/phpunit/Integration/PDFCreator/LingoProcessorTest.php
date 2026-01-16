<?php

namespace BlueSpice\DistributionConnector\tests\phpunit\Integration\PDFCreator;

use BlueSpice\DistributionConnector\Integration\PDFCreator\Processor\LingoProcessor;
use DOMDocument;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Extension\PDFCreator\Utility\ExportPage;
use MediaWiki\User\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Processor\LingoProcessor
 */
class LingoProcessorTest extends TestCase {

	/**
	 * @covers BlueSpice\DistributionConnector\Integration\PDFCreatorr\Processor\LingoProcessor::execute
	 */
	public function testExecute() {
		$pages = [];
		$exportContext = new ExportContext( User::newFromName( 'Testuser' ), null );

		$inputDom = new DOMDocument();
		$inputDom->loadHtmlFile( __DIR__ . '/data/LingoProcessorTest-input.html' );
		$pages[] = new ExportPage( 'page', $inputDom, 'Lingo test', 'Lingo_test', [] );

		$images = [];
		$attachments = [];
		$processor = new LingoProcessor();
		$processor->execute( $pages, $images, $attachments, $exportContext );

		$actualDom = $pages[0]->getDOMDocument();

		$expectedDom = new DOMDocument();
		$expectedDom->loadHtmlFile( __DIR__ . '/data/LingoProcessorTest-output.html' );

		$this->assertEquals(
			$actualDom->saveXML(),
			$expectedDom->saveXML(),
		);
	}

}
