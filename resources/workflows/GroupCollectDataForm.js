( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.GroupCollectData = function ( cfg, activity ) {
		bs.distributionConnector.workflows.form.GroupCollectData.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.GroupCollectData, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.GroupCollectData.prototype.getDefinitionItems = function () {
		return [
			{
				name: 'groupname',
				label: mw.message( 'bs-distributionconnector-workflows-form-groupname' ).text(),
				type: 'group_picker',
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
