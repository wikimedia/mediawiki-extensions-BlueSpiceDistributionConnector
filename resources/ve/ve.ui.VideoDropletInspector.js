ve.ui.VideoDropletInspector = function VeUiVideoDropletInspector( config ) {
	// Parent constructor
	ve.ui.VideoDropletInspector.super.call( this, ve.extendObject( { padded: true }, config ) );
};

/* Inheritance */
OO.inheritClass( ve.ui.VideoDropletInspector, ve.ui.MWLiveExtensionInspector );

/* Static properties */

ve.ui.VideoDropletInspector.static.name = 'videoDropletInspector';

ve.ui.VideoDropletInspector.static.title = 'Video';

ve.ui.VideoDropletInspector.static.modelClasses = [ ve.dm.VideoDropletNode ];

ve.ui.VideoDropletInspector.static.dir = 'ltr';

ve.ui.VideoDropletInspector.static.allowedEmpty = false;
ve.ui.VideoDropletInspector.static.selfCloseEmptyBody = false;

ve.ui.VideoDropletInspector.prototype.initialize = function () {
	ve.ui.VideoDropletInspector.super.prototype.initialize.call( this );

	this.indexLayout = new OO.ui.PanelLayout( {
		scrollable: false,
		expanded: false,
		padded: true
	} );

	this.createFields();

	this.setLayouts();

	// Initialization
	this.$content.addClass( 've-ui-videoDropletInspector-content' );

	this.indexLayout.$element.append(
		this.serviceLayout.$element,
		this.generatedContentsError.$element
	);
	this.form.$element.append(
		this.indexLayout.$element
	);
};

ve.ui.VideoDropletInspector.prototype.createFields = function () {
	var services = mw.config.get( 'bsgEmbedVideoServices' );

	var serviceData = [];
	for ( var service in services ) {
		var item =  new OO.ui.MenuOptionWidget( {
			data: services[ service ],
			label: services[ service ]
		} );
		serviceData.push( item );
	}
	this.serviceInput = new OO.ui.DropdownWidget( {
		menu: {
			items: serviceData
		}
	} );
	this.serviceInput.getMenu().selectItemByData( 'youtube' );
}

ve.ui.VideoDropletInspector.prototype.setLayouts = function () {
	this.serviceLayout = new OO.ui.FieldLayout( this.serviceInput, {
		align: 'right',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-service-label' ).plain()
	});
}

/**
 * @inheritdoc
 */
ve.ui.VideoDropletInspector.prototype.getSetupProcess = function ( data ) {
	return ve.ui.VideoDropletInspector.super.prototype.getSetupProcess.call( this, data )
		.next( function () {
			var attributes = this.selectedNode.getAttribute( 'mw' ).attrs;
			this.serviceInput.getMenu().selectItemByData( attributes.service || 'youtube' );

			this.actions.setAbilities( { done: true } );
		}, this );
};

ve.ui.VideoDropletInspector.prototype.updateMwData = function ( mwData ) {
	ve.ui.VideoDropletInspector.super.prototype.updateMwData.call( this, mwData );

	mwData.attrs.service = this.serviceInput.getMenu().findSelectedItem().getData();
};

/**
 * @inheritdoc
 */
ve.ui.VideoDropletInspector.prototype.formatGeneratedContentsError = function ($element) {
	return $element.text().trim();
};

/**
 * Append the error to the current tab panel.
 */
ve.ui.VideoDropletInspector.prototype.onTabPanelSet = function () {
	this.indexLayout.getCurrentTabPanel().$element.append(this.generatedContentsError.$element);
};

ve.ui.windowFactory.register(ve.ui.VideoDropletInspector);
