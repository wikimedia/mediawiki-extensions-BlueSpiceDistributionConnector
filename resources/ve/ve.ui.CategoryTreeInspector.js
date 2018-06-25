ve.ui.CategoryTreeInspector = function VeUiCategoryTreeInspector( config ) {
	// Parent constructor
	ve.ui.CategoryTreeInspector.super.call( this, ve.extendObject( { padded: true }, config ) );
};

/* Inheritance */

OO.inheritClass( ve.ui.CategoryTreeInspector, ve.ui.MWLiveExtensionInspector );

/* Static properties */

ve.ui.CategoryTreeInspector.static.name = 'categoryTreeInspector';

ve.ui.CategoryTreeInspector.static.title = OO.ui.deferMsg( 'bs-distribution-ve-categorytreeinpector-title' );

ve.ui.CategoryTreeInspector.static.modelClasses = [ ve.dm.CategoryTreeNode ];

ve.ui.CategoryTreeInspector.static.dir = 'ltr';

//This tag does not have any content
ve.ui.CategoryTreeInspector.static.allowedEmpty = false;
ve.ui.CategoryTreeInspector.static.selfCloseEmptyBody = false;

/* Methods */

/**
 * @inheritdoc
 */
ve.ui.CategoryTreeInspector.prototype.initialize = function () {

	// Parent method
	ve.ui.CategoryTreeInspector.super.prototype.initialize.call( this );

	// Index layout
	this.indexLayout = new OO.ui.PanelLayout( {
		scrollable: false,
		expanded: false,
		padded: true
	} );

	this.createFields();

	this.setLayouts();

	// Initialization
	this.$content.addClass( 've-ui-categoryTreeInspector-content' );

	this.indexLayout.$element.append(
		this.modeLayout.$element,
		this.depthLayout.$element,
		this.hideRootLayout.$element,
		this.hidePrefixLayout.$element,
		this.showCountLayout.$element,
		this.namespaceLayout.$element,
		this.styleLayout.$element,
		this.generatedContentsError.$element
	);
	this.form.$element.append(
		this.indexLayout.$element
	);
};

ve.ui.CategoryTreeInspector.prototype.createFields = function() {
	this.modeInput = new OO.ui.DropdownInputWidget( {
		options: [
			{
				data: '',
				label: ''
			},
			{
				data: 'categories',
				label: 'categories'
			},
			{
				data: 'pages',
				label: 'pages'
			},
			{
				data: 'all',
				label: 'all'
			},
			{
				data: 'parents',
				label: 'parents'
			}
		]
	} );
	this.depthInput = new OO.ui.NumberInputWidget( { min: 1, max: 50, isInteger: true } );
	this.hideRootInput = new OO.ui.ToggleSwitchWidget();
	this.hidePrefixInput = new OO.ui.DropdownInputWidget( {
		options: [
			{
				data: '',
				label: ''
			},
			{
				data: 'always',
				label: 'always'
			},
			{
				data: 'never',
				label: 'never'
			},
			{
				data: 'auto',
				label: 'auto'
			},
			{
				data: 'categories',
				label: 'categories'
			}
		]
	} );
	this.showCountInput = new OO.ui.ToggleSwitchWidget();
	this.namespaceInput = new OO.ui.TextInputWidget();
	this.styleInput = new OO.ui.TextInputWidget();
}

ve.ui.CategoryTreeInspector.prototype.setLayouts = function() {
	this.modeLayout = new OO.ui.FieldLayout( this.modeInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-dd-mode' )
	} );
	this.depthLayout = new OO.ui.FieldLayout( this.depthInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-tb-depth' )
	} );
	this.hideRootLayout = new OO.ui.FieldLayout( this.hideRootInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-tb-hideroot' )
	} );
	this.hidePrefixLayout = new OO.ui.FieldLayout( this.hidePrefixInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-dd-hideprefix' )
	} );
	this.showCountLayout = new OO.ui.FieldLayout( this.showCountInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-tb-showcount' )
	} );
	this.namespaceLayout = new OO.ui.FieldLayout( this.namespaceInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-tb-namespace' )
	} );
	this.styleLayout = new OO.ui.FieldLayout( this.styleInput, {
		align: 'right',
		label: ve.msg( 'bs-distribution-ve-categorytreeinspector-tb-style' )
	} );
}

/**
 * @inheritdoc
 */
ve.ui.CategoryTreeInspector.prototype.getSetupProcess = function ( data ) {
	return ve.ui.CategoryTreeInspector.super.prototype.getSetupProcess.call( this, data )
		.next( function () {
			var attributes = this.selectedNode.getAttribute( 'mw' ).attrs;

			this.modeInput.setValue( attributes.mode || '' );
			this.hidePrefixInput.setValue( attributes.hideprefix || '' );

			if( attributes.depth ) {
				this.depthInput.setValue( attributes.depth );
			}
			if( attributes.hideroot == 'on' ) {
				this.hideRootInput.setValue( true );
			}
			if( attributes.showcount == 'on' ) {
				this.showCountInput.setValue( true );
			}

			this.namespaceInput.setValue( attributes.namespaces || '' );
			this.styleInput.setValue( attributes.style || '' );

			//Get this out of here
			this.actions.setAbilities( { done: true } );
		}, this );
};

ve.ui.CategoryTreeInspector.prototype.updateMwData = function ( mwData ) {
	// Parent method
	ve.ui.CategoryTreeInspector.super.prototype.updateMwData.call( this, mwData );

	// Get data from inspector
	if( this.modeInput.getValue() !== '' ) {
		mwData.attrs.mode = this.modeInput.getValue();
	} else {
		delete( mwData.attrs.mode );
	}

	if( this.depthInput.getValue() ) {
		mwData.attrs.depth = this.depthInput.getValue();
	} else {
		delete( mwData.attrs.depth );
	}

	if( this.hideRootInput.getValue() === true ) {
		mwData.attrs.hideroot = 'on';
	} else {
		delete( mwData.attrs.hideroot );
	}

	if( this.hidePrefixInput.getValue() !== '' ) {
		mwData.attrs.hideprefix = this.hidePrefixInput.getValue();
	} else {
		delete( mwData.attrs.hideprefix );
	}

	if( this.showCountInput.getValue() === true ) {
		mwData.attrs.showcount = 'on';
	} else {
		delete( mwData.attrs.showcount );
	}

	if( this.namespaceInput.getValue() ) {
		mwData.attrs.namespaces = this.namespaceInput.getValue();
	} else {
		delete( mwData.attrs.namespaces );
	}

	if( this.styleInput.getValue() ) {
		mwData.attrs.style = this.styleInput.getValue();
	} else {
		delete( mwData.attrs.style );
	}

};

/**
 * @inheritdoc
 */
ve.ui.CategoryTreeInspector.prototype.formatGeneratedContentsError = function ( $element ) {
	return $element.text().trim();
};

/**
 * Append the error to the current tab panel.
 */
ve.ui.CategoryTreeInspector.prototype.onTabPanelSet = function () {
	this.indexLayout.getCurrentTabPanel().$element.append( this.generatedContentsError.$element );
};

/* Registration */

ve.ui.windowFactory.register( ve.ui.CategoryTreeInspector );
