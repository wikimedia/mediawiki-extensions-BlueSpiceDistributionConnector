( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.SubpagesDroplet = function ( cfg ) {
		bs.distributionConnector.object.SubpagesDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.SubpagesDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.SubpagesDroplet.prototype.templateMatches = function ( templateData ) {
		if ( !templateData ) {
			return false;
		}
		const target = templateData.target.wt;

		return target.trim( '\n' ) === 'Subpages' && this.getKey() === 'subpages';
	};

	bs.distributionConnector.object.SubpagesDroplet.prototype.toDataElement = function ( domElements, converter ) { // eslint-disable-line no-unused-vars
		return false;
	};

	bs.distributionConnector.object.SubpagesDroplet.prototype.getFormItems = function () {

		const formItems = [
			{
				name: 'parentnamespace',
				label: mw.message( 'bs-distributionconnector-droplets-subpages-namespace-label' ).text(),
				type: 'text',
				help: mw.message( 'droplets-subpages-namespace-help' ).text()
			},
			{
				name: 'parentpage',
				label: mw.message( 'bs-distributionconnector-droplets-subpages-parentpage-label' ).text(),
				type: 'text',
				help: mw.message( 'droplets-subpages-parentpage-help' ).text()
			},
			{
				name: 'cols',
				label: mw.message( 'bs-distributionconnector-droplets-subpages-cols-label' ).text(),
				help: mw.message( 'bs-distributionconnector-droplets-subpages-cols-help' ).text(),
				type: 'checkbox',
				labelAlign: 'inline'
			},
			{
				name: 'bullets',
				label: mw.message( 'bs-distributionconnector-droplets-subpages-bullets-label' ).text(),
				type: 'checkbox',
				labelAlign: 'inline'
			}
		];

		return formItems;
	};

	bs.distributionConnector.object.SubpagesDroplet.prototype.modifyFormDataBeforeSubmission =
	function ( dataPromise ) {
		// Convert true/false from checkbox control, to yes/no expected by the ButtonLink template
		const dfd = $.Deferred();
		dataPromise.done( ( data ) => {
			data.cols = data.cols ? 'yes' : 'no';
			data.bullets = data.bullets ? 'yes' : 'no';

			dfd.resolve( data );
		} ).fail( function () {
			dfd.reject( arguments );
		} );

		return dfd.promise();
	};

	bs.distributionConnector.object.SubpagesDroplet.prototype.getForm = function ( data ) {
		// convert yes/no to true and false for checkbox control
		if ( data.cols === 'yes' || data.cols === 'ja' ) {
			data.cols = true;
		} else {
			data.cols = false;
		}
		if ( data.bullets === 'yes' || data.bullets === 'ja' ) {
			data.bullets = true;
		} else {
			data.bullets = false;
		}
		return bs.distributionConnector.object.SubpagesDroplet.parent.prototype.getForm.call( this, data );
	};

	ext.contentdroplets.registry.register( 'subpages', bs.distributionConnector.object.SubpagesDroplet );

}( mediaWiki, jQuery, blueSpice ) );
