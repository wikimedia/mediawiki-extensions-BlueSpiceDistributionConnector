<?php

class BlueSpiceDistributionHooks {

	public static function onBeforePageDisplay( $out, $skin ) {
		global $wgScriptPath;
		if ( class_exists( "MobileContext" ) && MobileContext::singleton()->isMobileDevice() ) {
			$out->addHeadItem( 'bluespice.mobile', "<link rel='stylesheet' href='" . $wgScriptPath . "/extensions/BlueSpiceDistribution/BSDistConnector/resources/bluespice.mobile.css'>" );
		}
		$out->addModules( 'ext.bluespice.distribution' );
		$out->addModuleStyles( 'ext.bluespice.wikicategorytagcloud' );
		return true;
	}

	public static function onMinervaPreRender( MinervaTemplate $template ) {
		foreach ( $template->data['sidebar'] as $key => $val ) {
			if ( !is_array( $val ) ) {
				continue;
			}
			foreach ( $val as $key2 => $val2 ) {
				if ( strpos( $val2['text'], "|" ) ) {
					$aVal2 = explode( "|", $val2['text'] );
					$val2['text'] = $aVal2[0];
				}
				$template->data['discovery_urls'][$val2['id']] = $val2;
			}
		}
		$template->data['discovery_urls']['n-specialpages'] = array(
			'text' => wfMessage( "specialpages" )->plain(),
			'href' => SpecialPage::getSafeTitleFor( "Specialpages" )->getFullURL(),
			'id' => 'n-specialpages',
			'active' => false
		);
		return true;
	}

	public static function onResourceLoaderRegisterModules( ResourceLoader &$resourceLoader ) {
		global $wgResourceModules, $IP, $wgMFResourceBoilerplate;
		//MobileFrontend.php ln 189 sets it's ResourceLoader Paths without the possibility to overwrite
		//we don't want to hack our distribution extensions, so we're doing a modification here after it's been set
		foreach ( $wgResourceModules as $key => $val ) {
			$aKey = explode( ".", $key );
			if ( !isset( $aKey[1] ) ) continue;
			if ( $aKey[0] . "." . $aKey[1] != "skins.minerva" && $aKey[0] != "mobile" && $aKey[0] != "tablet" )
				continue;
			$wgResourceModules[$key]['localBasePath'] = $IP . "/extensions/BlueSpiceDistribution/MobileFrontend";
			$wgResourceModules[$key]['remoteExtPath'] = "BlueSpiceDistribution/MobileFrontend";
			$wgResourceModules[$key]['localTemplateBasePath'] = $IP . "/extensions/BlueSpiceDistribution/MobileFrontend/templates";
		}
		return true;
	}

	public static function onUserLoginForm( &$template ) {
		wfProfileIn( __METHOD__ );
		if ( $template instanceof UserLoginMobileTemplate ) {
			$template = new BSUserLoginMobileTemplate( $template );
		}
		wfProfileOut( __METHOD__ );
		return true;
	}

	/**
	 * This is an optional hook handler that needs to be enabled within BlueSpiceDistribution.php
	 * See https://www.mediawiki.org/wiki/Extension:LDAP_Authentication/Configuration_Options#Auto_authentication_options
	 * @param string $LDAPUsername
	 * @param array $info
	 * @return boolean
	 */
	public static function onSetUsernameAttribute( &$LDAPUsername, $info ) {
		$LDAPUsername = str_replace( '_', ' ', $info[0]['samaccountname'][0] );
		return true;
	}

	/**
	 * Inject CategoryTree tag into InsertMagic
	 * @param Object $oResponse reference
	 * $param String $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataCategoryTree( &$oResponse, $type ) {
		if ( $type != 'tags' ) return true;

		$oResponse->result[] = array(
			'id' => 'categorytree',
			'type' => 'tag',
			'name' => 'categorytree',
			'desc' => wfMessage( 'bs-distribution-tag-categorytree-desc' )->plain(),
			'code' => '<categorytree>Top_Level</categorytree>',
			'examples' => array(
				array(
					'code' => '<categorytree mode=pages>Manual</categorytree>'
				)
			),
			'helplink' => 'https://help.bluespice.com/index.php/CategoryTree'
		);

		return true;
	}

	/**
	 * Inject Cite tags into InsertMagic
	 * @param Object $oResponse reference
	 * $param String $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataCite( &$oResponse, $type ) {
		if ( $type != 'tags' ) return true;

		$oResponse->result[] = array(
			'id' => 'ref',
			'type' => 'tag',
			'name' => 'ref',
			'desc' => wfMessage( 'bs-distribution-tag-ref-desc' )->plain(),
			'code' => '<ref>Footnote text</ref>',
			'examples' => array(
				array(
					'code' => "Working with Wikis <ref>Wikis allow users not just to read an article but also to edit</ref>is fun. <br />
It is very useful to use footnotes <ref>A note can provide an author's comments on the main text or citations of a reference work</ref> in the articles.

==References==
<references/>
"
				)
			),
			'helplink' => 'https://help.bluespice.com/index.php/Cite'
		);

		$oResponse->result[] = array(
			'id' => 'references',
			'type' => 'tag',
			'name' => 'references',
			'desc' => wfMessage( 'bs-distribution-tag-references-desc' )->plain(),
			'code' => '<references />',
			'examples' => array(
				array(
					'code' => "Working with Wikis <ref>Wikis allow users not just to read an article but also to edit</ref>is fun. <br />
It is very useful to use footnotes <ref>A note can provide an author's comments on the main text or citations of a reference work</ref> in the articles.

==References==
<references/>
"
				)
			),
			'helplink' => 'https://help.bluespice.com/index.php/Cite'
		);

		return true;
	}

	/**
	 * Inject Quiz tag into InsertMagic
	 * @param Object $oResponse reference
	 * $param String $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataQuiz( &$oResponse, $type ) {
		if ( $type != 'tags' ) return true;

		$oResponse->result[] = array(
			'id' => 'quiz',
			'type' => 'tag',
			'name' => 'quiz',
			'desc' => wfMessage( 'bs-distribution-tag-quiz-desc' )->plain(),
			'code' => "<quiz>\n{ Your question }\n+ correct answer\n- incorrect answer\n</quiz>",
			'examples' => array(
				array(
					'code' => "<quiz>\n{ Your question }\n+ correct answer\n- incorrect answer\n</quiz>"
				)
			),
			'helplink' => 'https://help.bluespice.com/index.php/Quiz'
		);

		return true;
	}

	/**
	 * Inject EmbedVideo tag into InsertMagic
	 * @param Object $oResponse reference
	 * $param String $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataEmbedVideo( &$oResponse, $type ) {
		if ( $type != 'tags' ) return true;

		$oResponse->result[] = array(
			'id' => 'embedvideo',
			'type' => 'tag',
			'name' => 'embedvideo',
			'desc' => wfMessage( 'bs-distribution-tag-embedvideo-desc' )->plain(),
			'code' => '<embedvideo service="supported service">Link to video</embedvideo>',
			'examples' => array(
				array(
					'code' => "<embedvideo service=\"youtube\">https://www.youtube.com/watch?v=o3wZxqPZxyo</embedvideo>"
				)
			),
			'helplink' => 'https://www.mediawiki.org/wiki/Extension:EmbedVideo'
		);

		return true;
	}

	/**
	 * Inject Intersection tag into InsertMagic
	 * @param Object $oResponse reference
	 * $param String $type
	 * @return always true to keep hook running
	 */
	public static function onBSInsertMagicAjaxGetDataDynamicPageList( &$oResponse, $type ) {
		if ( $type != 'tags' ) return true;

		$oResponse->result[] = array(
			'id' => 'dynamicpagelist',
			'type' => 'tag',
			'name' => 'dynamicpagelist',
			'desc' => wfMessage( 'bs-distribution-tag-dynamicpagelist-desc' )->plain(),
			'code' => "<DynamicPageList>\ncategory = Demo\n</DynamicPageList>",
			'examples' => array(
				array(
					'code' => "<DynamicPageList>\ncategory = Pages recently transferred from Meta\ncount = 5\norder = ascending\naddfirstcategorydate = true\n</DynamicPageList>"
				)
			),
			'helplink' => 'https://www.mediawiki.org/wiki/Extension:DynamicPageList_%28Wikimedia%29#Use'
		);

		return true;
	}
}
