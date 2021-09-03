( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.FourEyesPrincipleCollectData = function( cfg, activity ) {
		bs.distributionConnector.workflows.form.FourEyesPrincipleCollectData.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.FourEyesPrincipleCollectData, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.FourEyesPrincipleCollectData.prototype.getDefinitionItems = function() {
		return [
			{
				name: 'usernameExpert',
				label: mw.message( 'bs-distributionconnector-workflows-form-expert-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsExpert',
				label: mw.message( 'bs-distributionconnector-workflows-form-expert-instructions' ).text(),
				type: 'wikitext',
			},
			{
				name: 'usernameHead',
				label: mw.message( 'bs-distributionconnector-workflows-form-head-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsHead',
				label: mw.message( 'bs-distributionconnector-workflows-form-head-instructions' ).text(),
				type: 'wikitext',
			},
			{
				name: 'reportrecipient',
				label: mw.message( 'bs-distributionconnector-workflows-form-reportrecipient' ).text(),
				type: 'text'
			}
		];
	};

} )( mediaWiki, jQuery, blueSpice );
