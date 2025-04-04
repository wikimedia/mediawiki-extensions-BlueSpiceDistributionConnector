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
				label: mw.message( 'droplets-subpages-namespace-label' ).plain(),
				type: 'text',
				help: mw.message( 'droplets-subpages-namespace-help' ).plain()
			},
			{
				name: 'parentpage',
				label: mw.message( 'droplets-subpages-parentpage-label' ).plain(),
				type: 'text',
				help: mw.message( 'droplets-subpages-parentpage-help' ).plain()
			},
			{
				name: 'cols',
				label: mw.message( 'droplets-subpages-cols-label' ).plain(),
				type: 'text',
				help: mw.message( 'droplets-subpages-cols-help' ).plain()
			},
			{
				name: 'bullets',
				label: mw.message( 'droplets-subpages-bullets-label' ).plain(),
				type: 'text',
				help: mw.message( 'droplets-subpages-bullets-help' ).plain()
			}
		];

		return formItems;
	};

	ext.contentdroplets.registry.register( 'subpages', bs.distributionConnector.object.SubpagesDroplet );

}( mediaWiki, jQuery, blueSpice ) );
