<?php

use BlueSpice\DistributionConnector\ContentImport\ImportLanguage;
use MediaWiki\Languages\LanguageFallback;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BlueSpice\DistributionConnector\ContentImport\ImportLanguage
 */
class ImportLanguageTest extends TestCase {

	/**
	 * @return array
	 */
	public function provideData() {
		return [
			'english' => [ 'en', 'en' ],
			'german' => [ 'de', 'de' ],
			'german formal' => [ 'de-formal', 'de' ],
			'russian' => [ 'ru', 'en' ],
			'portugal' => [ 'pt', 'pt-br' ],
			'unknown language' => [ 'unknown', 'en' ],
		];
	}

	/**
	 * @covers \BlueSpice\DistributionConnector\ContentImport\ImportLanguage::getImportLanguage()
	 * @dataProvider provideData
	 */
	public function testSuccess( $wikiContentLanguage, $expectedImportLanguage ) {
		$languageFallbackMock = $this->createMock( LanguageFallback::class );
		$languageFallbackMock->method( 'getAll' )->willReturnMap(
			[
				[ 'en', 0, [ 'en' ] ],
				[ 'de', 0, [ 'en' ] ],
				[ 'de-formal', 0, [ 'de', 'en' ] ],
				[ 'ru', 0, [ 'en' ] ],
				[ 'pt', 0, [ 'pt-br', 'en' ] ],
				[ 'gl', 0, [ 'pt', 'en' ] ],
				[ 'unknown', 0, [ 'en' ] ],
			]
		);

		$importLanguage = new ImportLanguage( $languageFallbackMock, $wikiContentLanguage );
		$availableLanguages = [ 'en', 'de', 'pt-br' ];

		$this->assertEquals( $expectedImportLanguage, $importLanguage->getImportLanguage( $availableLanguages ) );
	}
}
