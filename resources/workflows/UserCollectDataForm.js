( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.UserCollectData = function ( cfg, activity ) {
		bs.distributionConnector.workflows.form.UserCollectData.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.UserCollectData, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.UserCollectData.prototype.getDefinitionItems = function () {
		return [
			{
				name: 'username',
				label: mw.message( 'bs-distributionconnector-workflows-form-username' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructions',
				label: mw.message( 'bs-distributionconnector-workflows-form-instructions' ).text(),
				type: 'textarea'
			},
			{
				name: 'reportrecipient',
				label: mw.message( 'bs-distributionconnector-workflows-form-reportrecipient' ).text(),
				type: 'text'
			}
		];
	};

}( mediaWiki, jQuery, blueSpice ) );
