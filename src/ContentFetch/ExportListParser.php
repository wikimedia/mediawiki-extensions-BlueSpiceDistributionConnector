<?php

namespace BlueSpice\DistributionConnector\ContentFetch;

class ExportListParser {

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
		$pages = explode( "\n* ", $content );

		$pagesList = [];
		foreach ( $pages as $pageDataRaw ) {
			// Cut off "* " from the first line
			if ( strpos( $pageDataRaw, '* ' ) === 0 ) {
				$pageDataRaw = substr( $pageDataRaw, 2 );
			}

			$pageData = explode( "\n** ", $pageDataRaw );

			$title = trim( $pageData[0] );
			$title = str_replace( ' ', '_', $title );
			$lang = trim( $pageData[1] );
			$label = trim( $pageData[2] );
			$description = trim( $pageData[3] );

			$targetTitle = $title;
			if ( isset( $pageData[4] ) ) {
				$targetTitle = trim( $pageData[4] );
			}

			$lang = strtolower( $lang );

			$pagesList[$title] = [
				'lang' => $lang,
				'label' => $label,
				'description' => $description,
				'target_title' => $targetTitle
			];
		}

		return $pagesList;
	}
}
