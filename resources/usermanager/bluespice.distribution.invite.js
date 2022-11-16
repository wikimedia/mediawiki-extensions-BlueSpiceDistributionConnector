( function ( mw ) {

	mw.hook( 'usermanager.toolbar.init' ).add( function ( headerItems ) {
		inviteBtn = new Ext.Button( {
			id: 'btn-invite-user',
			icon: mw.config.get( 'wgScriptPath') + 
				'/extensions/BlueSpiceDistributionConnector/resources/images/inviteUser-icon.svg',
			iconCls: 'btn-invite-user',
			tooltip: mw.message( 'bs-distributionconnector-invite-signup-btn-tooltip-label' ).text(),
			ariaLabel: mw.message( 'bs-distributionconnector-invite-signup-btn-tooltip-label' ).text(),
			height: 50,
			width: 52
		} );
		inviteBtn.on( 'click', onBtnInviteClick, this );

		headerItems.push( inviteBtn );
	} );

	function onBtnInviteClick ( event ) {
		userInvite = new InviteUser();
		userInvite.show();
	}

}( mediaWiki ) );
