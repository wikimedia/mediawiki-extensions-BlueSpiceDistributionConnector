<?php

class BlueSpiceDistributionHooks {

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
