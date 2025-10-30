( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.MapDroplet = function ( cfg ) {
		bs.distributionConnector.object.MapDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.MapDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.MapDroplet.prototype.templateMatches = function ( templateData ) {
		if ( !templateData ) {
			return false;
		}
		const target = templateData.target.wt;
		return target.trim( '\n' ) === 'Map' && this.getKey() === 'map';
	};

	bs.distributionConnector.object.MapDroplet.prototype.toDataElement = function ( domElements, converter ) { // eslint-disable-line no-unused-vars
		return false;
	};

	bs.distributionConnector.object.MapDroplet.prototype.getFormItems = function () {
		const formItems = [
			{
				name: '2',
				label: mw.message( 'droplets-map-center-map-label' ).text(),
				help: mw.message( 'droplets-map-center-map-label-help' ).text(),
				type: 'textarea',
				row: 3
			},
			{
				name: '1',
				label: mw.message( 'droplets-map-center-label' ).text(),
				help: mw.message( 'droplets-map-center-label-help' ).text(),
				type: 'textarea',
				row: 1
			}
		];

		return formItems;
	};

	ext.contentdroplets.registry.register( 'map', bs.distributionConnector.object.MapDroplet );

}( mediaWiki, jQuery, blueSpice ) );
