( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.CreateInputDroplet = function ( cfg ) {
		bs.distributionConnector.object.CreateInputDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.CreateInputDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.CreateInputDroplet.prototype.templateMatches = function ( templateData ) {
		if ( !templateData ) {
			return false;
		}
		const target = templateData.target.wt;
		return target.trim( '\n' ) === 'CreateInput';
	};

	bs.distributionConnector.object.CreateInputDroplet.prototype.toDataElement = function ( domElements, converter ) { // eslint-disable-line no-unused-vars
		return false;
	};

	bs.distributionConnector.object.CreateInputDroplet.prototype.getFormItems = function () {
		return [
			{
				name: 'buttonlabel',
				label: mw.message( 'droplets-create-input-button-label' ).text(),
				type: 'text'
			},
			{
				name: 'preload',
				label: mw.message( 'droplets-create-input-preload-label' ).text(),
				help: mw.message( 'droplets-create-input-preload-help' ).text(),
				type: 'text'
			},
			{
				name: 'placeholder',
				label: mw.message( 'droplets-create-input-placeholder-label' ).text(),
				help: mw.message( 'droplets-create-input-placeholder-help' ).text(),
				type: 'text'
			},
			{
				name: 'prefix',
				label: mw.message( 'droplets-create-input-prefix-label' ).text(),
				help: mw.message( 'droplets-create-input-prefix-help' ).text(),
				type: 'text'
			},
			{
				name: 'alignment',
				label: mw.message( 'droplets-create-input-alignment-label' ).text(),
				help: mw.message( 'droplets-create-input-alignment-help' ).text(),
				type: 'dropdown',
				default: '',
				options: [
					{
						label: mw.message( 'droplets-create-input-alignment-left-label' ).text(),
						data: 'left'
					},
					{
						label: mw.message( 'droplets-create-input-alignment-right-label' ).text(),
						data: 'right'
					},
					{
						label: mw.message( 'droplets-create-input-alignment-center-label' ).text(),
						data: ''
					}
				]
			}
		];
	};

	ext.contentdroplets.registry.register( 'createInput', bs.distributionConnector.object.CreateInputDroplet );

}( mediaWiki, jQuery, blueSpice ) );
