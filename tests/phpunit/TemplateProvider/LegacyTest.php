<?php

namespace MediaWiki\Extension\PDFCreator\tests\phpunit;

use BlueSpice\DistributionConnector\Integration\PDFCreator\TemplateProvider\Legacy;
use MediaWiki\Config\ConfigFactory;
use MediaWiki\Config\HashConfig;
use MediaWiki\Extension\PDFCreator\ITemplateProvider;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;

/**
 * @covers \BlueSpice\DistributionConnector\Integration\PDFCreator\TemplateProvider\Legacy
 * @group Database
 */
class LegacyTest extends \MediaWikiIntegrationTestCase {

	/** @var ITemplateProvider */
	private $provider;

	/**
	 * @covers \BlueSpice\DistributionConnector\Integration\PDFCreator\TemplateProvider\Legacy::getTemplateNames
	 */
	public function testGetTemplateNames() {
		$provider = $this->getProvider();
		$actual = $provider->getTemplateNames();

		$this->assertEquals( [ 'TestTemplate' ], $actual );
	}

	/**
	 * @covers \BlueSpice\DistributionConnector\Integration\PDFCreator\TemplateProvider\Legacy::getTemplate
	 */
	public function testGetTemplate() {
		$provider = $this->getProvider();
		$testUser = $this->getTestUser();
		$context = new ExportContext( $testUser->getUser() );
		$template = $provider->getTemplate( $context );

		$resources = $template->getResources();
		$templatePath = dirname( __DIR__ ) . '/data/TemplateProvider/TestTemplate/';
		$commonPath = dirname( __DIR__ ) . '/data/TemplateProvider/common/';

		$actual = $resources->getFontPaths();
		$this->assertEquals( [
			'DejaVuSans.ttf' => $commonPath . 'fonts/DejaVuSans.ttf',
			'DejaVuSans-Bold.ttf' => $commonPath . 'fonts/DejaVuSans-Bold.ttf',
			'DejaVuSans-Oblique.ttf' => $commonPath . 'fonts/DejaVuSans-Oblique.ttf',
			'DejaVuSans-BoldOblique.ttf' => $commonPath . 'fonts/DejaVuSans-BoldOblique.ttf',
			'DejaVuSansMono.ttf' => $commonPath . 'fonts/DejaVuSansMono.ttf',
			'DejaVuSansMono-Bold.ttf' => $commonPath . 'fonts/DejaVuSansMono-Bold.ttf',
			'DejaVuSansMono-Oblique.ttf' => $commonPath . 'fonts/DejaVuSansMono-Oblique.ttf',
			'DejaVuSansMono-BoldOblique.ttf' => $commonPath . 'fonts/DejaVuSansMono-BoldOblique.ttf'
		], $actual );

		$actual = $resources->getStylesheetPaths();
		$this->assertEquals( [
			'page.css' => $commonPath . 'stylesheets/page.css',
			'mediawiki.css' => $commonPath . 'stylesheets/mediawiki.css',
			'styles.css' => $templatePath . 'stylesheets/styles.css',
			'geshi-php.css' => $commonPath . 'stylesheets/geshi-php.css',
			'tables.css' => $commonPath . 'stylesheets/tables.css',
			'fonts.css' => $commonPath . 'stylesheets/fonts.css',
		], $actual );

		$actual = $resources->getStyleBlocks();
		$this->assertEquals( [], $actual );

		$actual = $resources->getImagePaths();
		$this->assertEquals( [
			'logo.png' => $templatePath . 'images/logo.png'
		], $actual );

		$actual = $template->getIntro();
		$this->assertSame(
			'',
			$actual
		);
		$actual = $template->getHeader();
		$this->assertEquals(
			file_get_contents( $templatePath . 'header.html' ),
			$actual
		);
		$actual = $template->getFooter();
		$this->assertEquals(
			file_get_contents( $templatePath . 'footer.html' ),
			$actual
		);
		$actual = $template->getBody();
		$this->assertEquals(
			file_get_contents( $templatePath . 'body.html' ),
			$actual
		);
		$actual = $template->getOutro();
		$this->assertSame(
			'',
			$actual
		);
	}

	/**
	 * @return Local
	 */
	private function getProvider(): ITemplateProvider {
		if ( $this->provider instanceof Legacy ) {
			return $this->provider;
		}

		$configFactory = $this->createMock( ConfigFactory::class );
		$configFactory->method( 'makeConfig' )->willReturn( new HashConfig(
			[
				'PDFCreatorLegacyTemplateDirectory' => dirname( __DIR__ ) . '/data/TemplateProvider',
				'PDFCreatorDefaultLegacyTemplate' => 'TestTemplate',
			]
		) );

		$this->provider = new Legacy( $configFactory );
		return $this->provider;
	}

}
