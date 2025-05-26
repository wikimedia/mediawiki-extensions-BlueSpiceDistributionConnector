ve.ui.VideoDropletInspector = function VeUiVideoDropletInspector( config ) {
	// Parent constructor
	ve.ui.VideoDropletInspector.super.call( this, ve.extendObject( { padded: true }, config ) );
};

/* Inheritance */
OO.inheritClass( ve.ui.VideoDropletInspector, ve.ui.MWLiveExtensionInspector );

/* Static properties */

ve.ui.VideoDropletInspector.static.name = 'videoDropletInspector';

ve.ui.VideoDropletInspector.static.title =
	mw.message( 'bs-distributionconnector-videodropletinspector-title' ).text();

ve.ui.VideoDropletInspector.static.modelClasses = [ ve.dm.VideoDropletNode ];

ve.ui.VideoDropletInspector.static.dir = 'ltr';

ve.ui.VideoDropletInspector.static.allowedEmpty = false;
ve.ui.VideoDropletInspector.static.selfCloseEmptyBody = false;

ve.ui.VideoDropletInspector.prototype.initialize = function () {
	ve.ui.VideoDropletInspector.super.prototype.initialize.call( this );
	// remove input field
	this.input.$element.remove();

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
		this.inputLayout.$element,
		this.serviceLayout.$element,
		this.titleLayout.$element,
		this.descriptionLayout.$element,
		this.coverLayout.$element,
		this.dimensionLayout.$element,
		this.alignmentLayout.$element,
		this.containerLayout.$element,
		this.generatedContentsError.$element
	);
	this.form.$element.append(
		this.indexLayout.$element
	);

	this.defaultService = 'YouTube';
};

ve.ui.VideoDropletInspector.prototype.createFields = function () {
	this.input = new ve.ui.WhitespacePreservingTextInputWidget( {
		rows: 2
	} );

	const services = mw.config.get( 'bsgEmbedVideoServices' );

	const serviceData = [];
	for ( const service in services ) {
		const item = new OO.ui.MenuOptionWidget( {
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

	if ( services.includes( this.defaultService ) ) {
		this.serviceInput.getMenu().selectItemByData( this.defaultService );
	}

	this.titleInput = new OO.ui.TextInputWidget( {
		placeholder: mw.message( 'bs-distributionconnector-videodropletinspector-title-placeholder' ).plain()
	} );
	this.descriptionInput = new OO.ui.MultilineTextInputWidget( {
		rows: 2,
		placeholder: mw.message( 'bs-distributionconnector-videodropletinspector-description-placeholder' ).plain()
	} );
	this.coverInput = new OOJSPlus.ui.widget.FileSearchWidget( {
		extensions: [ 'svg', 'png', 'jpg' ],
		placeholder: mw.message( 'bs-distributionconnector-videodropletinspector-cover-placeholder' ).plain()
	} );
	this.dimensionsInput = new OO.ui.TextInputWidget( {
		placeholder: '640'
	} );

	const alignmentsData = [ 'inline', 'left', 'center', 'right' ];
	const alignments = [];
	for ( const alignmentKey in alignmentsData ) {
		const alignment = alignmentsData[ alignmentKey ];
		const item = new OO.ui.MenuOptionWidget( {
			data: alignment,
			// bs-distributionconnector-videodropletinspector-alignment-inline
			// bs-distributionconnector-videodropletinspector-alignment-left
			// bs-distributionconnector-videodropletinspector-alignment-center
			// bs-distributionconnector-videodropletinspector-alignment-right
			// eslint-disable-next-line mediawiki/msg-doc
			label: mw.message(
				'bs-distributionconnector-videodropletinspector-alignment-' + alignment ).plain()
		} );
		alignments.push( item );
	}
	this.alignmentInput = new OO.ui.DropdownWidget( {
		menu: {
			items: alignments
		}
	} );
	this.alignmentInput.getMenu().selectItemByData( 'left' );
	this.containerInput = new OO.ui.CheckboxInputWidget( {
		value: 'frame'
	} );
};

ve.ui.VideoDropletInspector.prototype.setLayouts = function () {
	this.inputLayout = new OO.ui.FieldLayout( this.input, {
		align: 'top',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-video-link-label' ).plain()
	} );
	this.serviceLayout = new OO.ui.FieldLayout( this.serviceInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-service-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-service-help' ).plain()
	} );
	this.titleLayout = new OO.ui.FieldLayout( this.titleInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-title-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-title-help' ).plain()
	} );
	this.descriptionLayout = new OO.ui.FieldLayout( this.descriptionInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-description-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-description-help' ).plain()
	} );
	this.coverLayout = new OO.ui.FieldLayout( this.coverInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-cover-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-cover-help' ).plain()
	} );
	this.dimensionLayout = new OO.ui.FieldLayout( this.dimensionsInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-dimension-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-dimension-help' ).plain()
	} );
	this.alignmentLayout = new OO.ui.FieldLayout( this.alignmentInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-alignment-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-alignent-help' ).plain()
	} );
	this.containerLayout = new OO.ui.FieldLayout( this.containerInput, {
		align: 'left',
		label: mw.message( 'bs-distributionconnector-videodropletinspector-container-label' ).plain(),
		help: mw.message( 'bs-distributionconnector-videodropletinspector-container-help' ).plain()
	} );
};

/**
 * @inheritdoc
 */
ve.ui.VideoDropletInspector.prototype.getSetupProcess = function ( data ) {
	return ve.ui.VideoDropletInspector.super.prototype.getSetupProcess.call( this, data )
		.next( function () {
			const attributes = this.selectedNode.getAttribute( 'mw' ).attrs;

			this.serviceInput.getMenu().selectItemByData( attributes.service || this.defaultService );

			if ( attributes.title ) {
				this.titleInput.setValue( attributes.title );
			}
			if ( attributes.description ) {
				this.descriptionInput.setValue( attributes.description );
			}
			if ( attributes.cover ) {
				this.coverInput.setValue( attributes.cover );
			}
			if ( attributes.dimensions ) {
				this.dimensionsInput.setValue( attributes.dimensions );
			}
			if ( attributes.alignment ) {
				this.alignmentInput.getMenu().selectItemByData( attributes.alignment );
			}
			if ( attributes.container === 'frame' ) {
				this.containerInput.setSelected( true );
			}

			this.actions.setAbilities( { done: true } );
		}, this );
};

ve.ui.VideoDropletInspector.prototype.updateMwData = function ( mwData ) {
	ve.ui.VideoDropletInspector.super.prototype.updateMwData.call( this, mwData );

	mwData.attrs.service = this.serviceInput.getMenu().findSelectedItem().getData();
	mwData.attrs.alignment = this.alignmentInput.getMenu().findSelectedItem().getData();

	if ( this.titleInput.getValue() ) {
		mwData.attrs.title = this.titleInput.getValue();
	} else {
		delete ( mwData.attrs.title );
	}
	if ( this.descriptionInput.getValue() !== '' ) {
		mwData.attrs.description = this.descriptionInput.getValue();
	} else {
		delete ( mwData.attrs.description );
	}
	if ( this.coverInput.getValue() ) {
		mwData.attrs.cover = this.coverInput.getValue();
	} else {
		delete ( mwData.attrs.cover );
	}
	if ( this.dimensionsInput.getValue() !== '' ) {
		mwData.attrs.dimensions = this.dimensionsInput.getValue();
	} else {
		delete ( mwData.attrs.dimensions );
	}
	if ( this.containerInput.isSelected() ) {
		mwData.attrs.container = this.containerInput.getValue();
	} else {
		delete ( mwData.attrs.container );
	}
};

/**
 * @inheritdoc
 */
ve.ui.VideoDropletInspector.prototype.formatGeneratedContentsError = function ( $element ) {
	return $element.text().trim();
};

/**
 * Append the error to the current tab panel.
 */
ve.ui.VideoDropletInspector.prototype.onTabPanelSet = function () {
	this.indexLayout.getCurrentTabPanel().$element.append( this.generatedContentsError.$element );
};

ve.ui.windowFactory.register( ve.ui.VideoDropletInspector );
