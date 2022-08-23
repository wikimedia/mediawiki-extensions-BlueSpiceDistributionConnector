( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.ButtonLinkDroplet = function( cfg ) {
		bs.distributionConnector.object.ButtonLinkDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.ButtonLinkDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.templateMatches = function( templateData ) {
		if ( !templateData ) {
			return false;
		}
		var target = templateData.target.wt;
		return target.trim( '\n' ) === 'ButtonLink' && 'buttonlink' === this.getKey();
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.toDataElement = function( domElements, converter  ) {
		return false;
	};

	bs.distributionConnector.object.ButtonLinkDroplet.prototype.getFormItems = function() {
		return [
			{
				name: 'external',
				label: mw.message( 'droplets-buttonlink-external-label' ).plain(),
				type: 'text'
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
				type: 'text'
			}
		];
	};

	ext.contentdroplets.registry.register( 'buttonlink', bs.distributionConnector.object.ButtonLinkDroplet );

} )( mediaWiki, jQuery, blueSpice );
