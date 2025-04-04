( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.ButtonLinkDroplet = function ( cfg ) {
		bs.distributionConnector.object.ButtonLinkDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.ButtonLinkDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.templateMatches = function ( templateData ) {
		if ( !templateData ) {
			return false;
		}
		const target = templateData.target.wt;
		return target.trim( '\n' ) === 'ButtonLink' && this.getKey() === 'buttonlink';
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.toDataElement = function ( domElements, converter ) { // eslint-disable-line no-unused-vars
		return false;
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.getFormItems = function () {
		return [
			{
				name: 'external',
				label: mw.message( 'droplets-buttonlink-external-label' ).plain(),
				type: 'checkbox'
			},
			{
				name: 'target',
				label: mw.message( 'droplets-buttonlink-target-label' ).plain(),
				type: 'text'
			},
			{
				name: 'label',
				label: mw.message( 'droplets-buttonlink-label-label' ).plain(),
				type: 'text'
			},
			{
				name: 'format',
				label: mw.message( 'droplets-buttonlink-format-label' ).plain(),
				type: 'dropdown',
				options: [
					{
						data: 'blue',
						label: mw.message( 'droplets-buttonlink-format-blue' ).plain()
					},
					{
						data: 'neutral',
						label: mw.message( 'droplets-buttonlink-format-neutral' ).plain()
					},
					{
						data: 'red',
						label: mw.message( 'droplets-buttonlink-format-red' ).plain()
					}
				]
			}
		];
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.modifyFormDataBeforeSubmission =
	function ( dataPromise ) {
		// Convert true/false from checkbox control, to yes/no expected by the ButtonLink template
		const dfd = $.Deferred();
		dataPromise.done( ( data ) => {
			data.external = data.external ? 'yes' : 'no';
			dfd.resolve( data );
		} ).fail( function () {
			dfd.reject( arguments );
		} );

		return dfd.promise();
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.getForm = function ( data ) {
		// convert yes/no to true and false for checkbox control
		if ( data.external === 'no' ) {
			data.external = false;
		} else {
			data.external = true;
		}
		return bs.distributionConnector.object.ButtonLinkDroplet.parent.prototype.getForm.call( this, data );
	};

	ext.contentdroplets.registry.register( 'buttonlink', bs.distributionConnector.object.ButtonLinkDroplet );

}( mediaWiki, jQuery, blueSpice ) );
