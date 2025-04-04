( function ( mw ) {
	mw.hook( 'usermanager.toolbar.init' ).add( ( actions ) => {
		actions.push( new OOJSPlus.ui.toolbar.tool.ToolbarTool( {
			name: 'invite',
			displayBothIconAndLabel: true,
			icon: 'message',
			hidden: true,
			title: mw.msg( 'bs-distributionconnector-invite-signup-btn-tooltip-label' ),
			callback: function () {
				OO.ui.confirm( mw.msg( 'bs-distributionconnector-invite-signup-dialog-text' ) ).done(
					( confirmed ) => {
						this.setActive( false );
						if ( !confirmed ) {
							return;
						}
						window.location.href = mw.Title.makeTitle( -1, 'InviteSignup' ).getUrl();
					}
				);
			}
		} ) );
	} );

}( mediaWiki ) );
