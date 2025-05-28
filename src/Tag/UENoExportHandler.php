<?php

namespace BlueSpice\DistributionConnector\Tag;

use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;
use MWStake\MediaWiki\Component\GenericTagHandler\ITagHandler;

class UENoExportHandler implements ITagHandler {

	/**
	 * @inheritDoc
	 */
	public function getRenderedContent( string $input, array $params, Parser $parser, PPFrame $frame ): string {
		return '<div class="pdfcreator-excludestart"></div>' .
			$input .
			'<div class="pdfcreator-excludeend"></div>';
	}
}
