Ext.define( 'InviteUser', {
	extend: 'BS.SimpleDialog',
	idPrefix: 'invite-user',
	titleMsg: 'bs-distributionconnector-invite-signup-dialog-title',

	afterInitComponent: function () {
		this.callParent( arguments );
		this.cntMain.add( {
			html: mw.message( 'bs-distributionconnector-invite-signup-dialog-text' ).text(),
			border: false,
			margin: '5px'
		} );

		this.buttons = [
			this.btnCancel,
			this.btnOK
		];
	},
	btnOKClicked: function() {
		this.callParent( arguments );
		window.location.href = mw.Title.newFromText( 'Special:InviteSignup' ).getUrl();
	},

} );
