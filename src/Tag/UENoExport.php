<?php

namespace BlueSpice\DistributionConnector\Tag;

use BlueSpice\Tag\Tag;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\PPFrame;

class UENoExport extends Tag {

	/**
	 *
	 * @param string $processedInput
	 * @param array $processedArgs
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return UENoExportHandler
	 */
	public function getHandler( $processedInput, array $processedArgs, Parser $parser,
		PPFrame $frame ) {
		return new UENoExportHandler(
			$processedInput,
			$processedArgs,
			$parser,
			$frame
		);
	}

	/**
	 *
	 * @return string[]
	 */
	public function getTagNames() {
		return [
			'bs:uenoexport'
		];
	}

	/**
	 *
	 * @return string
	 */
	public function getContainerElementName() {
		return 'div';
	}
}
