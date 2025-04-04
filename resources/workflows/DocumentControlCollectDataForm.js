( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.DocumentControlCollectDataForm = function ( cfg, activity ) {
		bs.distributionConnector.workflows.form.DocumentControlCollectDataForm.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.DocumentControlCollectDataForm, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.DocumentControlCollectDataForm.prototype.getDefinitionItems = function () {
		return [
			{
				name: 'usernameEditor',
				label: mw.message( 'bs-distributionconnector-workflows-form-label-editor' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsEditor',
				label: mw.message( 'bs-distributionconnector-workflows-form-instructions-editor' ).text(),
				type: 'textarea'
			},
			{
				name: 'usernameReviewer',
				label: mw.message( 'bs-distributionconnector-workflows-form-label-reviewer' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsReviewer',
				label: mw.message( 'bs-distributionconnector-workflows-form-instructions-reviewer' ).text(),
				type: 'textarea'
			},
			{
				name: 'usernameApprover',
				label: mw.message( 'bs-distributionconnector-workflows-form-label-approver' ).text(),
				type: 'user_picker',
				required: true
			},
			{
				name: 'instructionsApprover',
				label: mw.message( 'bs-distributionconnector-workflows-form-instructions-approver' ).text(),
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
