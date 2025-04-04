( function ( mw, $, bs ) {

	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.PDFLinkDroplet = function ( cfg ) {
		bs.distributionConnector.object.PDFLinkDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.PDFLinkDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.PDFLinkDroplet.prototype.templateMatches = function ( templateData ) {
		if ( !templateData ) {
			return false;
		}
		const target = templateData.target.wt;
		return target.trim( '\n' ) === 'PDFLink';
	};

	bs.distributionConnector.object.PDFLinkDroplet.prototype.toDataElement = function ( domElements, converter ) { // eslint-disable-line no-unused-vars
		return false;
	};

	bs.distributionConnector.object.PDFLinkDroplet.prototype.getFormItems = function () {
		const config = require( './config.json' );
		const templates = [];
		for ( const entry in config.templates ) {
			const item = {
				data: config.templates[ entry ]
			};
			templates.push( item );
		}

		return [
			{
				name: 'page',
				label: mw.message( 'droplets-pdf-link-page-label' ).plain(),
				type: 'title'
			},
			{
				name: 'template',
				label: mw.message( 'droplets-pdf-link-template-label' ).plain(),
				type: 'dropdown',
				default: config.default,
				options: templates
			},
			{
				name: 'label',
				label: mw.message( 'droplets-pdf-link-link-label' ).plain(),
				type: 'text'
			}
		];
	};

	ext.contentdroplets.registry.register( 'pdflink', bs.distributionConnector.object.PDFLinkDroplet );

}( mediaWiki, jQuery, blueSpice ) );
