( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.CircledNumberDroplet = function ( cfg ) {
		bs.distributionConnector.object.CircledNumberDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.CircledNumberDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.CircledNumberDroplet.prototype.templateMatches = function ( templateData ) {
		if ( !templateData ) {
			return false;
		}
		const target = templateData.target.wt;
		return target.trim( '\n' ) === 'CircledNumber' && this.getKey() === 'circled-number';
	};

	bs.distributionConnector.object.CircledNumberDroplet.prototype.toDataElement = function ( domElements, converter ) { // eslint-disable-line no-unused-vars
		return false;
	};

	bs.distributionConnector.object.CircledNumberDroplet.prototype.getFormItems = function () {
		return [
			{
				name: 'bgColor',
				label: mw.message( 'droplets-circled-number-bg-color-label' ).plain(),
				type: 'text'
			},
			{
				name: 'fgColor',
				label: mw.message( 'droplets-circled-number-fg-color-label' ).plain(),
				type: 'text'
			},
			{
				name: 'number',
				label: mw.message( 'droplets-circled-number-label' ).plain(),
				type: 'text'
			}
		];
	};

	ext.contentdroplets.registry.register( 'circled-number', bs.distributionConnector.object.CircledNumberDroplet );

}( mediaWiki, jQuery, blueSpice ) );
