<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use MediaWiki\Content\Hook\ContentAlterParserOutputHook;
use MediaWiki\Content\WikitextContent;

class AddVariablesPageProperty implements ContentAlterParserOutputHook {

	/**
	 * @inheritDoc
	 */
	public function onContentAlterParserOutput( $content, $title, $parserOutput	) {
		if ( $title->getContentModel() !== CONTENT_MODEL_WIKITEXT ) {
			return;
		}
		if ( !( $content instanceof WikitextContent ) ) {
			return;
		}

		$text = $content->getText();
		$regex = '/\{\{#var/';

		if ( preg_match( $regex, $text ) ) {
			$parserOutput->setPageProperty( 'variables', '1' );
		} else {
			$parserOutput->unsetPageProperty( 'variables' );
		}
	}
}
