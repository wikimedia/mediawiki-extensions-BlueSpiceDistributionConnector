<?php

namespace BlueSpice\DistributionConnector\tests\phpunit\HookHandler;

use BlueSpice\DistributionConnector\HookHandler\ResourceLoaderRegisterModules;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * @covers \BlueSpice\DistributionConnector\HookHandler\ResourceLoaderRegisterModules
 */
class ResourceLoaderRegisterModulesTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @dataProvider provideExtensionsLoadedRLModules
	 */
	public function testConditionalRegistration( $loadedExtensions, $expectedModules ) {
		$extensionRegistry = $this->createMock( ExtensionRegistry::class );
		$extensionRegistry->method( 'isLoaded' )
			->willReturnCallback( static function ( $ext ) use ( $loadedExtensions ) {
				return in_array( $ext, $loadedExtensions );
			} );

		// mock extension registry
		$rlModules = [];

		$resourceLoader = $this->createMock( ResourceLoader::class );
		$resourceLoader->method( 'register' )
			 ->willReturnCallback( static function ( array $modules ) use ( &$rlModules ) {
				 $rlModules = array_merge( $rlModules, $modules );
			 } );

		( new ResourceLoaderRegisterModules( $extensionRegistry ) )
			->onResourceLoaderRegisterModules( $resourceLoader );

		// assertEquals() shortens the strings which hides the full module name
		// in the diff.
		$this->assertSame(
			$expectedModules,
			array_keys( $rlModules )
		);
	}

	public static function provideExtensionsLoadedRLModules() {
		$workflows = [
			'ext.bluespice.distribution.workflows.trigger.editor',
		];
		$contentDroplets = [
			'ext.bluespice.distribution.droplet.subpages',
			'ext.bluespice.distribution.droplet.circlednumber',
			'ext.bluespice.distribution.droplet.gallery',
			'ext.bluespice.distribution.droplet.createInput',
			'ext.bluespice.distribution.droplet.pdflink',
		];

		// [loaded extensions..], [expected RL modules]
		return [
			'no extensions' => [
				[], [],
			],
			'solely ContentDroplets' => [
				[ 'ContentDroplets' ],
				$contentDroplets,
			],
			'BlueSpiceSMWConnector and Workflows' => [
				[ 'BlueSpiceSMWConnector', 'Workflows' ],
				$workflows,
			],
			'Workflows, BlueSpiceSMWConnector and ContentDroplets' => [
				[ 'Workflows', 'BlueSpiceSMWConnector', 'ContentDroplets' ],
				array_merge( $workflows, $contentDroplets ),
			],
			'BlueSpiceSMWConnector' => [
				[], [],
			],
			'Workflows' => [
				[], [],
			],
		];
	}
}
