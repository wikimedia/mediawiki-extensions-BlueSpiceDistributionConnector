<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderRegisterModulesHook;
use MediaWiki\ResourceLoader\ResourceLoader;

class ResourceLoaderRegisterModules implements ResourceLoaderRegisterModulesHook {

	private ExtensionRegistry $extensionRegistry;

	public function __construct(
		ExtensionRegistry $extensionRegistry,
	) {
		$this->extensionRegistry = $extensionRegistry;
	}

	/**
	 * @inheritDoc
	 */
	public function onResourceLoaderRegisterModules( ResourceLoader $rl ): void {
		$localBasePath = dirname( __DIR__, 2 ) . '/resources';
		$remoteExtPath = 'BlueSpiceDistributionConnector/resources';

		if (
			$this->extensionRegistry->isLoaded( 'Workflows' )
			&& $this->extensionRegistry->isLoaded( 'BlueSpiceSMWConnector' )
		) {
			$rl->register( [
				'ext.bluespice.distribution.workflows.trigger.editor' => [
					'localBasePath' => $localBasePath,
					'remoteExtPath' => $remoteExtPath,
					'scripts' => [
						'workflows/ui/trigger/TimeSMWProperty.js',
					],
					'dependencies' => [
						'ext.workflows.trigger.editors',
						'ext.BSSMWConnector.widgets',
					],
					'messages' => [
						'bs-distributionconnector-workflows-ui-trigger-field-property',
						'bs-distributionconnector-workflows-ui-trigger-field-days',
					],
				],
			] );
		}

		if ( $this->extensionRegistry->isLoaded( 'ContentDroplets' ) ) {
			$rl->register( [
				'ext.bluespice.distribution.droplet.subpages' => [
					'localBasePath' => $localBasePath,
					'remoteExtPath' => $remoteExtPath,
					'scripts' => [
						'object/SubpagesDroplet.js',
					],
					'styles' => [
						'stylesheets/bluespice.contentdroplets.subpages.css',
					],
					'messages' => [
						'droplets-subpages-namespace-help',
						'droplets-subpages-parentpage-help',
						'bs-distributionconnector-droplets-subpages-namespace-label',
						'bs-distributionconnector-droplets-subpages-parentpage-label',
						'bs-distributionconnector-droplets-subpages-cols-label',
						'bs-distributionconnector-droplets-subpages-cols-help',
						'bs-distributionconnector-droplets-subpages-bullets-label',
					],
				],
				'ext.bluespice.distribution.droplet.circlednumber' => [
					'localBasePath' => $localBasePath,
					'remoteExtPath' => $remoteExtPath,
					'scripts' => [
						'object/CircledNumberDroplet.js',
					],
					'styles' => [
						'stylesheets/bluespice.contentdroplets.circled-number.css',
					],
					'dependencies' => [
						'ext.contentdroplets.bootstrap',
					],
					'messages' => [
						'droplets-circled-number-bg-color-label',
						'droplets-circled-number-fg-color-label',
						'droplets-circled-number-label',
						'bs-distributionconnector-droplets-circled-number-color-help',
					],
				],
				'ext.bluespice.distribution.droplet.gallery' => [
					'localBasePath' => $localBasePath,
					'remoteExtPath' => $remoteExtPath,
					'styles' => [
						'stylesheets/bluespice.contentdroplets.gallery.css',
					],
				],
				'ext.bluespice.distribution.droplet.createInput' => [
					'localBasePath' => $localBasePath,
					'remoteExtPath' => $remoteExtPath,
					'scripts' => [
						'object/CreateInputDroplet.js',
					],
					'styles' => [
						'stylesheets/bluespice.contentdroplets.createInput.css',
					],
					'messages' => [
						'droplets-create-input-button-label',
						'droplets-create-input-preload-label',
						'droplets-create-input-preload-help',
						'droplets-create-input-placeholder-label',
						'droplets-create-input-placeholder-help',
						'droplets-create-input-prefix-label',
						'droplets-create-input-prefix-help',
						'droplets-create-input-alignment-label',
						'droplets-create-input-alignment-help',
						'droplets-create-input-alignment-left-label',
						'droplets-create-input-alignment-right-label',
						'droplets-create-input-alignment-center-label',
					],
				],
				'ext.bluespice.distribution.droplet.pdflink' => [
					'localBasePath' => $localBasePath,
					'remoteExtPath' => $remoteExtPath,
					'packageFiles' => [
						'object/PDFLinkDroplet.js',
						[
							'name' => 'object/config.json',
							'callback' => '\\BlueSpice\\DistributionConnector\\ClientConfig::getPDFTemplates',
						],
					],
					'styles' => [
						'stylesheets/bluespice.contentdroplets.pdflink.css',
					],
					'dependencies' => [
						'ext.contentdroplets.bootstrap',
					],
					'messages' => [
						'droplets-pdf-link-page-label',
						'droplets-pdf-link-template-label',
						'droplets-pdf-link-link-label',
					],
				],
			] );
		}
	}
}
