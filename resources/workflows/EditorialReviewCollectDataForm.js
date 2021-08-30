( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.EditorialReviewCollectData = function( cfg, activity ) {
		bs.distributionConnector.workflows.form.EditorialReviewCollectData.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.EditorialReviewCollectData, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.EditorialReviewCollectData.prototype.getDefinitionItems = function() {
		return [
			{
				name: 'usernameExpert',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-one-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsExpert',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-one-instructions' ).text(),
				type: 'textarea',
			},
			{
				name: 'usernameTechnicalWriter',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-two-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsTechnicalWriter',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-two-instructions' ).text(),
				type: 'textarea'
			},
			{
				name: 'usernameHead',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-three-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsHead',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-three-instructions' ).text(),
				type: 'textarea'
			}
		];
	};

} )( mediaWiki, jQuery, blueSpice );
