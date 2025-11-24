<?php

namespace BlueSpice\DistributionConnector\tests\phpunit\Integration\PDFCreator\Utility;

use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerAttachmentFinder;
use DOMDocument;
use File;
use MediaWiki\Extension\PDFCreator\Utility\ExportPage;
use MediaWiki\Extension\PDFCreator\Utility\WikiFileResource;
use MediaWiki\MainConfigNames;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;
use MediaWiki\Utils\UrlUtils;
use MediaWikiLangTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RepoGroup;

/**
 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerAttachmentFinder
 */
class PDFHandlerAttachmentFinderTest extends MediaWikiLangTestCase {

	/**
	 * @covers BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerAttachmentFinder::execute
	 */
	public function testExecute() {
		$this->overrideConfigValues( [
			MainConfigNames::UploadDirectory => '/var/www/bluespice/w/images',
			MainConfigNames::UploadPath => '/pdfcreator/images',
			MainConfigNames::ScriptPath => '/pdfcreator',
		] );

		$services = $this->getServiceContainer();
		$titleFactory = $this->mockTitleFactory();
		$config = $services->getMainConfig();
		$repoGroup = $this->mockRepoGroup();
		$fileFinder = new PDFHandlerAttachmentFinder( $titleFactory, $config, $repoGroup );

		$actual = $fileFinder->execute( $this->getPages() );
		$expected = [
			new WikiFileResource(
				[
					'/pdfcreator/images/7/77/Test.pdf'
				],
				'/var/www/pdfcreator/images/7/77/Test.pdf',
				'Test.pdf'
			)
		];
		$this->assertEquals( $expected, $actual );
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
		$html = file_get_contents( dirname( __DIR__ ) . '/data/PDFHandlerAttachmentFinder-input.html' );
		$dom = new DOMDocument();
		$dom->loadXML( $html );
		return $dom;
	}

	/**
	 * @return MockObject|RepoGroup&MockObject
	 */
	private function mockRepoGroup() {
		$imageInfo = $this->getImages();
		$localRepoMock = $this->getMockBuilder( RepoGroup::class )
			->disableOriginalConstructor()
			->getMock();
		$localRepoMock->method( 'findFile' )->willReturnCallback( function ( $fileTitle )  use ( $imageInfo ) {
			$name = $fileTitle->getDbKey();
			$image = $imageInfo[$name] ?? null;
			if ( !$image ) {
				return null;
			}
			$revId = max( array_keys( $image ) );
			$image = $image[$revId];
			$imageMock = $this->getMockBuilder( File::class )
				->disableOriginalConstructor()
				->getMock();
			$imageMock->method( 'getTitle' )->willReturnCallback( function () use ( $revId ) {
				$titleMock = $this->getMockBuilder( Title::class )
					->disableOriginalConstructor()
					->getMock();
				$titleMock->method( 'getLatestRevID' )->willReturn( $revId );
				return $titleMock;
			} );
			$imageMock->method( 'getName' )->willReturn( $name );
			$imageMock->method( 'getTimestamp' )->willReturn( $image['timestamp'] );
			$imageMock->method( 'getSha1' )->willReturn( $image['sha1'] );
			$imageMock->method( 'getLocalRefPath' )->willReturn( $image['localRefPath'] );
			return $imageMock;
		} );

		return $localRepoMock;
	}

	/**
	 * @return MockObject|UrlUtils&MockObject
	 */
	private function mockUrlUtils() {
		$localUrlUtilsMock = $this->getMockBuilder( UrlUtils::class )
			->disableOriginalConstructor()
			->getMock();
		$localUrlUtilsMock->method( 'expand' )->willReturnCallback( static function ( $href ) {
			return 'http://www.example.com/pdfcreator/index.php?title=File:Test.pdf';
		} );
		$localUrlUtilsMock->method( 'parse' )->willReturnCallback( static function ( $href ) {
			return [
				'query' => 'title=File:Test.pdf',
				'path' => 'pdfcreator/index.php',
				'server' => 'www.example.com',
				'scheme' => 'http'
			];
		} );
		return $localUrlUtilsMock;
	}

	/**
	 * @return MockObject|TitleFactory&MockObject
	 */
	private function mockTitleFactory() {
		$localTitleFactory = $this->getMockBuilder( TitleFactory::class )
			->disableOriginalConstructor()
			->getMock();
		$localTitleFactory->method( 'newFromDBkey' )->willReturnCallback( function ( $text ) {
			$titleMock = $this->getMockBuilder( Title::class )
				->disableOriginalConstructor()
				->getMock();

			$titleMock->method( 'getLocalUrl' )->willReturnCallback( static function () {
				return '/pdfcreator/images/7/77/Test.pdf';
			} );
			$titleMock->method( 'getNamespace' )->willReturnCallback( static function () {
				return NS_FILE;
			} );
			$titleMock->method( 'getDBkey' )->willReturnCallback( static function () {
				return 'Test.pdf';
			} );
			$titleMock->method( 'getPrefixedText' )->willReturnCallback( static function () {
				return 'Test.pdf';
			} );
			return $titleMock;
		} );
		return $localTitleFactory;
	}

	private function getImages(): array {
		return [
			'page1-300px-Test.pdf.jpg' => [
				1 => [
					'timestamp' => '20210101000000',
					'sha1' => 'sha1:1234567890abcdef1234567890abcdef12345678',
					'localRefPath' => '/var/www/pdfcreator/images/thumb/7/77/Test.pdf/page1-300px-Test.pdf.jpg',
				],
			],
			'Test.pdf' => [
				1 => [
					'timestamp' => '20210101000000',
					'sha1' => 'sha1:1234567890abcdef1234567890abcdef12345678',
					'localRefPath' => '/var/www/pdfcreator/images/7/77/Test.pdf',
				],
			],
		];
	}
}
