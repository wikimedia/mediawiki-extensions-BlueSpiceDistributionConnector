<?php

namespace BlueSpice\DistributionConnector\tests\phpunit\Integration\PDFCreator\Utility;

use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFinder;
use DOMDocument;
use MediaWiki\Extension\PDFCreator\Utility\ExportPage;
use MediaWiki\MainConfigNames;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\TitleFactory;
use MediaWikiLangTestCase;
use RepoGroup;

/**
 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFinder
 */
class PDFHandlerThumbFinderTest extends MediaWikiLangTestCase {

	/**
	 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFinder::execute
	 */
	public function testExecute() {
		$this->overrideConfigValues( [
			MainConfigNames::UploadDirectory => '/var/www/bluespice/w/images',
			MainConfigNames::UploadPath => '/pdfcreator/images',
			MainConfigNames::ScriptPath => '/pdfcreator',
		] );

		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();

		$pages = $this->getPages();

		$resolver = new PDFHandlerThumbFinder(
			$config,
			$this->createMock( TitleFactory::class ),
			$services->getUrlUtils(),
			$this->createMock( RepoGroup::class )
		);
		$data = $resolver->execute( $pages, [] );
		$this->assertEquals( 'page1-600px-Test.pdf.jpg', $data[ 0 ]->getFilename() );

		// PDFHandlerThumbFinder marked found img tags with class "pdfhandler-thumb"
		$thumb = $pages[ 0 ]->getDOMDocument()->getElementsByTagName( 'img' );
		$class = $thumb[ 0 ]->getAttribute( 'class' );
		$this->assertEquals( 'mw-file-element pdfhandler-thumb', $class );
	}

	/**
	 * @return ExportPage[]
	 */
	private function getPages(): array {
		$page = new ExportPage( 'raw', $this->getDom(), 'PDFHandler test page' );
		return [ $page ];
	}

	/**
	 * @return DOMDocument
	 */
	private function getDOM(): DOMDocument {
		$html = file_get_contents( dirname( __DIR__ ) . '/data/PDFHandlerThumbFileResolver-input.html' );
		$dom = new DOMDocument();
		$dom->loadXML( $html );
		return $dom;
	}
}
