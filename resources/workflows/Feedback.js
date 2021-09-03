( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.form' );
	bs.distributionConnector.workflows.form.Feedback = function( cfg, activity ) {
		bs.distributionConnector.workflows.form.Feedback.parent.call( this, cfg, activity );
	};

	OO.inheritClass( bs.distributionConnector.workflows.form.Feedback, workflows.object.form.Form );

	bs.distributionConnector.workflows.form.Feedback.prototype.getDefinitionItems = function() {
		return [
			{
				name: 'instructions',
				type: 'static_wikitext',
				noLayout: true
			},
			{
				name: 'feedback',
				placeholder: mw.message( 'bs-distributionconnector-workflows-form-feedback' ).text(),
				noLayout: true,
				type: 'wikitext',
				required: true
			}
		];
	};

} )( mediaWiki, jQuery, blueSpice );
