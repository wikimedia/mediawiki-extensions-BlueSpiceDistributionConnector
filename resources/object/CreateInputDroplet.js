( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.object' );

	bs.distributionConnector.object.CreateInputDroplet = function( cfg ) {
		bs.distributionConnector.object.CreateInputDroplet.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.object.CreateInputDroplet, ext.contentdroplets.object.TransclusionDroplet );

	bs.distributionConnector.object.CreateInputDroplet.prototype.templateMatches = function( templateData ) {
		if ( !templateData ) {
			return false;
		}
		var target = templateData.target.wt;
		return target.trim( '\n' ) === 'CreateInput';
	};

	bs.distributionConnector.object.CreateInputDroplet.prototype.toDataElement = function( domElements, converter  ) {
		return false;
	};

	bs.distributionConnector.object.CreateInputDroplet.prototype.getFormItems = function() {
		return [
			{
				name: 'buttonlabel',
				label: mw.message( 'droplets-create-input-button-label' ).plain(),
				type: 'text'
			},
			{
				name: 'preload',
				label: mw.message( 'droplets-create-input-preload-label' ).plain(),
				type: 'textarea',
				rows: 5
			},
			{
				name: 'placeholder',
				label: mw.message( 'droplets-create-input-placeholder-label' ).plain(),
				type: 'text'
			},
			{
				name: 'prefix',
				label: mw.message( 'droplets-create-input-prefix-label' ).plain(),
				type: 'text'
			},
			{
				name: 'alignment',
				label: mw.message( 'droplets-create-input-alignment-label' ).plain(),
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
						label: '',
						data: ''
					}
				]
			}
		];
	};

	ext.contentdroplets.registry.register( 'createInput', bs.distributionConnector.object.CreateInputDroplet );

} )( mediaWiki, jQuery, blueSpice );