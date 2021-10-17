( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.DocumentControlCollectDataForm = function( cfg, activity ) {
		bs.distributionConnector.workflows.form.DocumentControlCollectDataForm.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.DocumentControlCollectDataForm, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.DocumentControlCollectDataForm.prototype.getDefinitionItems = function() {
		return [
			{
				name: 'usernameEditor',
				label: mw.message( 'bs-distributionconnector-workflows-form-expert-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsEditor',
				label: mw.message( 'bs-distributionconnector-workflows-form-expert-instructions' ).text(),
				type: 'wikitext',
			},
			{
				name: 'usernameReviewer',
				label: mw.message( 'bs-distributionconnector-workflows-form-tw-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsReviewer',
				label: mw.message( 'bs-distributionconnector-workflows-form-tw-instructions' ).text(),
				type: 'wikitext'
			},
			{
				name: 'usernameApprover',
				label: mw.message( 'bs-distributionconnector-workflows-form-head-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsApprover',
				label: mw.message( 'bs-distributionconnector-workflows-form-head-instructions' ).text(),
				type: 'wikitext'
			},
			{
				name: 'reportrecipient',
				label: mw.message( 'bs-distributionconnector-workflows-form-reportrecipient' ).text(),
				type: 'text'
			}
		];
	};

} )( mediaWiki, jQuery, blueSpice );
