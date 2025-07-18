<?php

namespace BlueSpice\DistributionConnector\tests\phpunit\Integration\PDFCreator\Utility;

use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFileResolver;
use DOMDocument;
use MediaWiki\MainConfigNames;
use MediaWiki\MediaWikiServices;
use MediaWikiLangTestCase;

/**
 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFileResolver
 */
class PDFHandlerThumbFileResolverTest extends MediaWikiLangTestCase {

	/**
	 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFileResolver::execute
	 */
	public function testExecute() {
		$this->overrideConfigValues( [
			MainConfigNames::UploadDirectory => '/var/www/bluespice/w/images',
			MainConfigNames::UploadPath => '/pdfcreator/images',
			MainConfigNames::ScriptPath => '/pdfcreator',
		] );

		$services = MediaWikiServices::getInstance();
		$config = $services->getMainConfig();

		$dom = $this->getDOM();

		$resolver = new PDFHandlerThumbFileResolver( $config );
		$data = $resolver->execute( $dom->getElementsByTagName( 'img' )->item( 0 ) );

		$filename = '';
		$absPath = '';
		if ( $data !== null ) {
			$filename = $data['filename'];
			$absPath = $data['absPath'];
		}
		$this->assertEquals( 'page1-600px-Test.pdf.jpg', $filename );
		$this->assertEquals( '/var/www/bluespice/w/images/thumb/7/77/Test.pdf/page1-600px-Test.pdf.jpg', $absPath );
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
