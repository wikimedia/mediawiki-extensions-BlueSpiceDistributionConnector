<?php

namespace BlueSpice\DistributionConnector;

use MediaWiki\Config\Config;
use MediaWiki\MediaWikiServices;
use MediaWiki\ResourceLoader\Context;

class ClientConfig {

	/**
	 * @param Context $context
	 * @param Config $config
	 * @return array
	 */
	public static function getPDFTemplates( Context $context, Config $config ): array {
		$services = MediaWikiServices::getInstance();
		$titleFactory = $services->getTitleFactory();
		$pdfCreatorUtil = $services->getService( 'PDFCreator.Util' );
		$templates = $pdfCreatorUtil->getAllWikiTemplates();

		$template = $config->get( 'PDFCreatorDefaultTemplate' );
		$templateTitle = $titleFactory->newFromText( 'MediaWiki:PDFCreator/' . $template );
		if ( !$templateTitle->exists() ) {
			return [
				'templates' => $templates,
				'default' => ''
			];
		}

		return [
			'templates' => $templates,
			'default' => $template
		];
	}
}
