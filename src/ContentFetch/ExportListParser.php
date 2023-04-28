<?php

namespace BlueSpice\DistributionConnector\ContentFetch;

class ExportListParser {

	/**
	 * Title data attributes will be processed.
	 * They will be processed in exactly that order.
	 *
	 * @var string[]
	 */
	private $attributes = [
		'lang',
		'label',
		'description',
		'target_title'
	];

	/**
	 * Parses list obtained from the source wiki "export list" page
	 *
	 * @param string $content
	 * @return array Array with such structure:
	 * [
	 * 		page_title1 => [
	 *			'lang' => $lang,
	 * 			'label' => $label,
	 * 			'description' => $description,
	 * 			'target_title' => $targetTitle
	 * 		],
	 * 		page_title2 => [
	 * 		...
	 * 		],
	 * 		...
	 * ]
	 * @see \BSFetchContents::readExportList()
	 */
	public function parse( string $content ): array {
		$pagesList = [];

		// Pointer to current processing title
		$processingTitle = null;

		// Number
		$attributeNumber = 0;

		$lines = explode( "\n", $content );
		foreach ( $lines as $line ) {
			$line = trim( $line );

			// Skip empty lines or lines which does not start with '*'
			if ( $line === '' || $line[0] !== '*' ) {
				continue;
			}

			// If there is only one asterisk - it's title line
			// All next data attributes will be linked to that title
			if ( $line[1] !== '*' ) {
				$processingTitle = trim( substr( $line, 1 ) );
				$processingTitle = str_replace( ' ', '_', $processingTitle );

				// Reset attributes counter
				$attributeNumber = 0;

				continue;
			}

			$attribute = trim( substr( $line, 2 ) );
			$attributeName = $this->attributes[$attributeNumber];

			// Language should be lowered case
			if ( $attributeName === 'lang' ) {
				$attribute = strtolower( $attribute );
			}

			$pagesList[$processingTitle][$attributeName] = $attribute;

			$attributeNumber++;
		}

		// If target title was not specified, use source title
		foreach ( $pagesList as $title => &$data ) {
			if ( !isset( $data['target_title'] ) ) {
				$data['target_title'] = $title;
			}
		}

		return $pagesList;
	}
}
