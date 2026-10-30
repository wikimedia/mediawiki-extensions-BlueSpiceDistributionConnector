<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use MediaWiki\Output\Hook\OutputPageParserOutputHook;

/**
 * Template:ButtonLink renders Codex CSS-only buttons. A template cannot load
 * ResourceLoader modules, so add the Codex button styles wherever it is used.
 */
class AddButtonLinkResources implements OutputPageParserOutputHook {

	private const MARKER = 'bs-buttonlink';

	private const STYLE_MODULE = 'ext.bluespice.distribution.buttonlink.styles';

	/**
	 * @inheritDoc
	 */
	public function onOutputPageParserOutput( $out, $parserOutput ): void {
		if ( !$parserOutput->hasText() ) {
			return;
		}
		if ( strpos( $parserOutput->getContentHolderText(), self::MARKER ) === false ) {
			return;
		}
		$out->addModuleStyles( [ self::STYLE_MODULE ] );
	}
}
