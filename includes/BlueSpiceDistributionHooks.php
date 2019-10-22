<?php

class BlueSpiceDistributionHooks {

	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function onBeforePageDisplay( $out, $skin ) {
		$out->addModules( 'ext.bluespice.distribution' );
		return true;
	}

	/**
	 * This is an optional hook handler that needs to be enabled within BlueSpiceDistribution.php
	 * See https://www.mediawiki.org/wiki/Extension:LDAP_Authentication/Configuration_Options#Auto_authentication_options
	 * @param string &$LDAPUsername
	 * @param array $info
	 * @return bool
	 */
	public static function onSetUsernameAttribute( &$LDAPUsername, $info ) {
		$LDAPUsername = str_replace( '_', ' ', $info[0]['samaccountname'][0] );
		return true;
	}

	/**
	 * Inject CategoryTree tag into InsertMagic
	 * @param \stdClass &$oResponse reference
	 * @param string $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataCategoryTree( &$oResponse, $type ) {
		if ( $type != 'tags' ) { return true;
		}

		$oResponse->result[] = [
			'id' => 'categorytree',
			'type' => 'tag',
			'name' => 'categorytree',
			'desc' => wfMessage( 'bs-distributionconnector-tag-categorytree-desc' )->plain(),
			'mwvecommand' => 'categoryTreeCommand',
			'code' => '<categorytree>Top_Level</categorytree>',
			'examples' => [
				[
					'code' => '<categorytree mode=pages>Manual</categorytree>'
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:CategoryTree'
		];

		return true;
	}

	/**
	 * Inject Cite tags into InsertMagic
	 * @param \stdClass &$oResponse reference
	 * @param string $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataCite( &$oResponse, $type ) {
		if ( $type != 'tags' ) { return true;
		}

		$oResponse->result[] = [
			'id' => 'ref',
			'type' => 'tag',
			'name' => 'ref',
			'desc' => wfMessage( 'bs-distributionconnector-tag-ref-desc' )->plain(),
			'code' => '<ref>Footnote text</ref>',
			'examples' => [
				[
					'code' => "Working with Wikis <ref>Wikis allow users not just to "
						. "read an article but also to edit</ref>is fun. <br />
It is very useful to use footnotes <ref>A note can provide an author's comments on the "
. "main text or citations of a reference work</ref> in the articles.

==References==
<references/>
"
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:Cite'
		];

		$oResponse->result[] = [
			'id' => 'references',
			'type' => 'tag',
			'name' => 'references',
			'desc' => wfMessage( 'bs-distributionconnector-tag-references-desc' )->plain(),
			'code' => '<references />',
			'examples' => [
				[
					'code' => "Working with Wikis <ref>Wikis allow users not just to "
						. "read an article but also to edit</ref>is fun. <br />
It is very useful to use footnotes <ref>A note can provide an author's comments on "
. "the main text or citations of a reference work</ref> in the articles.

==References==
<references/>
"
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:Cite'
		];

		return true;
	}

	/**
	 * Inject Quiz tag into InsertMagic
	 * @param \stdClass &$oResponse reference
	 * @param string $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataQuiz( &$oResponse, $type ) {
		if ( $type != 'tags' ) { return true;
		}

		$oResponse->result[] = [
			'id' => 'quiz',
			'type' => 'tag',
			'name' => 'quiz',
			'desc' => wfMessage( 'bs-distributionconnector-tag-quiz-desc' )->plain(),
			'code' => "<quiz>\n{ Your question }\n+ correct answer\n- incorrect answer\n</quiz>",
			'examples' => [
				[
					'code' => "<quiz>\n{ Your question }\n+ correct answer\n- incorrect answer\n</quiz>"
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:Quiz'
		];

		return true;
	}

	/**
	 * Inject EmbedVideo tag into InsertMagic
	 * @param \stdClass &$oResponse reference
	 * @param string $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataEmbedVideo( &$oResponse, $type ) {
		if ( $type != 'tags' ) { return true;
		}

		$oResponse->result[] = [
			'id' => 'embedvideo',
			'type' => 'tag',
			'name' => 'embedvideo',
			'desc' => wfMessage( 'bs-distributionconnector-tag-embedvideo-desc' )->plain(),
			'code' => '<embedvideo service="supported service">Link to video</embedvideo>',
			'examples' => [
				[
					'code' => "<embedvideo service=\"youtube\">"
						. "https://www.youtube.com/watch?v=o3wZxqPZxyo"
						. "</embedvideo>"
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:EmbedVideo'
		];

		return true;
	}

	/**
	 * Inject Intersection tag into InsertMagic
	 * @param \stdClass &$oResponse reference
	 * @param string $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataDynamicPageList( &$oResponse, $type ) {
		if ( $type != 'tags' ) { return true;
		}

		$oResponse->result[] = [
			'id' => 'dynamicpagelist',
			'type' => 'tag',
			'name' => 'dynamicpagelist',
			'desc' => wfMessage( 'bs-distributionconnector-tag-dynamicpagelist-desc' )->plain(),
			'code' => "<DynamicPageList>\ncategory = Demo\n</DynamicPageList>",
			'examples' => [
				[
					'code' => "<DynamicPageList>
category = Pages recently transferred from Meta
count = 5
order = ascending
addfirstcategorydate = true
</DynamicPageList>"
				]
			],
			'helplink' => 'https://www.mediawiki.org/wiki/Extension:DynamicPageList_%28Wikimedia%29#Use'
		];

		return true;
	}

	/**
	 * @param BaseTemplate $baseTemplate
	 * @param array &$toolbox
	 * @return bool
	 */
	public static function onBaseTemplateToolbox( BaseTemplate $baseTemplate, array &$toolbox ) {
		global $wgHooks;

		// Hook might not be set. If this is the case, skip the rest of the function
		if ( !isset( $wgHooks['SkinTemplateToolboxEnd'] ) ) {
			return true;
		}

		// Move duplicater toolbox link from legacy hook to
		// SkinTemplateToolboxEnd
		$iPosDuplicatior = array_search(
			'efDuplicatorToolbox',
			$wgHooks['SkinTemplateToolboxEnd']
		);

		if ( $iPosDuplicatior !== false
			&& !empty( $baseTemplate->data['nav_urls']['duplicator']['href'] ) ) {
			unset( $wgHooks['SkinTemplateToolboxEnd'][$iPosDuplicatior] );
			$toolbox['duplicator'] = [
				"id" => "t-duplicator",
				"href" => $baseTemplate->data['nav_urls']['duplicator']['href'],
				"text" => wfMessage( 'duplicator-toolbox' )->plain(),
			];
		}

		// Move cite toolbox link from legacy hook to SkinTemplateToolboxEnd
		// Move duplicater toolbox link from legacy hook to
		// SkinTemplateToolboxEnd
		$iPosCiteThisPage = array_search(
			"CiteThisPageHooks::onSkinTemplateToolboxEnd",
			$wgHooks['SkinTemplateToolboxEnd']
		);

		if ( $iPosCiteThisPage !== false && !empty( $baseTemplate->data['nav_urls']['citeThisPage'] ) ) {
			unset( $wgHooks['SkinTemplateToolboxEnd'][$iPosCiteThisPage] );
			$toolbox['citethispage'] = array_merge(
				[
					"id" => "t-cite",
					"href" => SpecialPage::getTitleFor( 'CiteThisPage' )->getLocalURL(
						$baseTemplate->data['nav_urls']['citeThisPage']['args']
					),
					"text" => wfMessage( 'citethispage-link' )->escaped(),
				],
				Linker::tooltipAndAccessKeyAttribs( 'citethispage' )
			);
		}

		return true;
	}
}
