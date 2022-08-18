( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.MapDroplet = function( cfg ) {
		bs.distributionConnector.object.MapDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.MapDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.MapDroplet.prototype.templateMatches = function( templateData ) {
		if ( !templateData ) {
			return false;
		}
		var target = templateData.target.wt;
		return target.trim( '\n' ) === 'Map' && 'map' === this.getKey();
	};


	bs.distributionConnector.object.MapDroplet.prototype.toDataElement = function( domElements, converter  ) {
		return false;
	};

	bs.distributionConnector.object.MapDroplet.prototype.getFormItems = function() {

		var formItems = [
			{
				name: '1',
				label: mw.message( 'droplets-map-condition-label' ).plain(),
				type: 'textarea',
				row: 1
			},
			{
				name: '2',
				label:  mw.message( 'droplets-map-center-label' ).plain(),
				type: 'textarea',
				row: 3
			}
		];

		return formItems;
	};

	ext.contentdroplets.registry.register( 'map', bs.distributionConnector.object.MapDroplet );

} )( mediaWiki, jQuery, blueSpice );