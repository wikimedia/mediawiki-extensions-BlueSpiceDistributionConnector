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
				label: mw.message( 'droplets-create-input-button-label' ).plain(),
				type: 'text'
			},
			{
				name: 'preload',
				label: mw.message( 'droplets-create-input-preload-label' ).plain(),
				help: mw.message( 'droplets-create-input-preload-help' ).plain(),
				type: 'text'
			},
			{
				name: 'placeholder',
				label: mw.message( 'droplets-create-input-placeholder-label' ).plain(),
				help: mw.message( 'droplets-create-input-placeholder-help' ).plain(),
				type: 'text'
			},
			{
				name: 'prefix',
				label: mw.message( 'droplets-create-input-prefix-label' ).plain(),
				help: mw.message( 'droplets-create-input-prefix-help' ).plain(),
				type: 'text'
			},
			{
				name: 'alignment',
				label: mw.message( 'droplets-create-input-alignment-label' ).plain(),
				help: mw.message( 'droplets-create-input-alignment-help' ).plain(),
				type: 'dropdown',
				default: '',
				options: [
					{
						label: mw.message( 'droplets-create-input-alignment-left-label' ).plain(),
						data: 'left'
					},
					{
						label: mw.message( 'droplets-create-input-alignment-right-label' ).plain(),
						data: 'right'
					},
					{
						label: mw.message( 'droplets-create-input-alignment-center-label' ).plain(),
						data: ''
					}
				]
			}
		];
	};

	ext.contentdroplets.registry.register( 'createInput', bs.distributionConnector.object.CreateInputDroplet );

}( mediaWiki, jQuery, blueSpice ) );
