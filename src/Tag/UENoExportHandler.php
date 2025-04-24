<?php

namespace BlueSpice\DistributionConnector\Tag;

use BlueSpice\Tag\Handler;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;

class UENoExportHandler extends Handler {

	/**
	 * @param string $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 */
	public function __construct( $processedInput, array $processedArgs, Parser $parser,
		PPFrame $frame ) {
		parent::__construct( $processedInput, $processedArgs, $parser, $frame );
	}

	/**
	 *
	 * @return string
	 */
	public function handle() {
		return '<div class="pdfcreator-excludestart"></div>' .
			$this->processedInput .
			'<div class="pdfcreator-excludeend"></div>';
	}
}
