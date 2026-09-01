<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use MediaWiki\Output\Hook\OutputPageParserOutputHook;

class AddCreateInputResources implements OutputPageParserOutputHook {

	private const MARKER = 'mw-inputbox-createbox';

	private const STYLE_MODULE = 'ext.bluespice.distribution.inputbox.createEnhance.styles';

	private const SCRIPT_MODULE = 'ext.bluespice.distribution.inputbox.createEnhance';

	/**
	 * @inheritDoc
	 */
	public function onOutputPageParserOutput( $out, $parserOutput ): void {
		if ( !$parserOutput->hasText() ) {
			return;
		}
		if ( strpos( $parserOutput->getRawText(), self::MARKER ) === false ) {
			return;
		}
		$out->addModuleStyles( [ self::STYLE_MODULE ] );
		$out->addModules( [ self::SCRIPT_MODULE ] );
	}
}
