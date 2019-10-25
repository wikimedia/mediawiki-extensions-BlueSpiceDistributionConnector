<?php

class BlueSpiceDistributionHooks {

	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function onBeforePageDisplay( $out, $skin ) {
		if ( class_exists( "MobileContext" ) && MobileContext::singleton()->isMobileDevice() ) {
			$out->addHeadItem(
				'bluespice.mobile',
				"<link rel='stylesheet' href='"
					. $skin->getConfig()->get( 'ScriptPath' )
					. "/extensions/BlueSpiceDistribution/BSDistConnector/resources/bluespice.mobile.css'>"
			);
		}
		$out->addModules( 'ext.bluespice.distribution' );
		return true;
	}

	/**
	 *
	 * @param MinervaTemplate $template
	 * @return bool
	 */
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
		$template->data['discovery_urls']['n-specialpages'] = [
			'text' => wfMessage( "specialpages" )->plain(),
			'href' => SpecialPage::getSafeTitleFor( "Specialpages" )->getFullURL(),
			'id' => 'n-specialpages',
			'active' => false
		];
		return true;
	}

	/**
	 *
	 * @param UsercreateTemplate|UserLoginMobileTemplate &$template
	 * @return bool
	 */
	public static function onUserLoginForm( &$template ) {
		if ( $template instanceof UserLoginMobileTemplate ) {
			$template = new BSUserLoginMobileTemplate( $template );
		}
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
