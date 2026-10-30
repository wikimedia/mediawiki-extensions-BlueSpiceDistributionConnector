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
				name: 'target',
				label: mw.message( 'droplets-buttonlink-target-label' ).text(),
				type: 'text'
			},
			{
				name: 'label',
				label: mw.message( 'droplets-buttonlink-label-label' ).text(),
				type: 'text'
			},
			{
				name: 'format',
				label: mw.message( 'droplets-buttonlink-format-label' ).text(),
				type: 'dropdown',
				options: [
					{
						data: 'blue',
						label: mw.message( 'droplets-buttonlink-format-blue' ).text()
					},
					{
						data: 'neutral',
						label: mw.message( 'droplets-buttonlink-format-neutral' ).text()
					},
					{
						data: 'red',
						label: mw.message( 'droplets-buttonlink-format-red' ).text()
					}
				]
			}
		];
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.modifyFormDataBeforeSubmission =
	function ( dataPromise ) {
		// Set "external" from the target, so links with any URL protocol (e.g. mailto:) work
		const dfd = $.Deferred();
		const urlProtocols = new RegExp( '^(' + mw.config.get( 'wgUrlProtocols' ) + ')', 'i' );
		dataPromise.done( ( data ) => {
			data.external = urlProtocols.test( ( data.target || '' ).trim() ) ? 'yes' : 'no';
			dfd.resolve( data );
		} ).fail( function () {
			dfd.reject( arguments );
		} );

		return dfd.promise();
	};

	ext.contentdroplets.registry.register( 'buttonlink', bs.distributionConnector.object.ButtonLinkDroplet );

}( mediaWiki, jQuery, blueSpice ) );
