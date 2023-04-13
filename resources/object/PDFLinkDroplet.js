( function ( mw, $, bs ) {

	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.PDFLinkDroplet = function( cfg ) {
		bs.distributionConnector.object.PDFLinkDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.PDFLinkDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.PDFLinkDroplet.prototype.templateMatches = function( templateData ) {
		if ( !templateData ) {
			return false;
		}
		var target = templateData.target.wt;
		return target.trim( '\n' ) === 'PDFLink';
	};

	bs.distributionConnector.object.PDFLinkDroplet.prototype.toDataElement = function( domElements, converter  ) {
		return false;
	};

	bs.distributionConnector.object.PDFLinkDroplet.prototype.getFormItems = function() {
		var defaultTemplate = mw.config.get( 'bsUEModulePDFDefaultTemplate' );
		var availableTemplate = mw.config.get( 'bsUEModulePDFAvailableTemplates');

		var templates = [];
		for ( var entry in availableTemplate ) {
			var item =  {
				data: availableTemplate[ entry ]
			};
			templates.push( item );
		}

		return [
			{
				name: 'page',
				label: mw.message( 'droplets-pdf-link-page-label' ).plain(),
				type: 'text'
			},
			{
				name: 'template',
				label: mw.message( 'droplets-pdf-link-template-label' ).plain(),
				type: 'dropdown',
				default: defaultTemplate,
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

} )( mediaWiki, jQuery, blueSpice );
