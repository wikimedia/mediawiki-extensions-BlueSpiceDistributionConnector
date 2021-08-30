( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.ThreeStepApprovalCollectData = function( cfg, activity ) {
		bs.distributionConnector.workflows.form.ThreeStepApprovalCollectData.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.ThreeStepApprovalCollectData, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.ThreeStepApprovalCollectData.prototype.getDefinitionItems = function() {
		return [
			{
				name: 'groupname',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-one-group' ).text(),
				type: 'group_picker',
				required: true
			},
			{
				name: 'instructionsGroup',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-one-instructions' ).text(),
				type: 'textarea',
			},
			{
				name: 'usernameExpert',
				label: mw.message( 'bs-distributionconnector-workflows-form-step-two-user' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsExpert',
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
