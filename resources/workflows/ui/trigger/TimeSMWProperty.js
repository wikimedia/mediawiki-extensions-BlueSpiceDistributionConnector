( function ( mw, $ ) {
	bs.util.registerNamespace( 'bs.distributionConnector.workflows.trigger' );

	bs.distributionConnector.workflows.trigger.TimeSMWProperty = function ( data, cfg ) {
		bs.distributionConnector.workflows.trigger.TimeSMWProperty.parent.call( this, data, cfg );
		workflows.ui.trigger.mixin.WorkflowSelector.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.workflows.trigger.TimeSMWProperty, workflows.ui.trigger.PageRelated );
	OO.mixinClass( bs.distributionConnector.workflows.trigger.TimeSMWProperty, workflows.ui.trigger.mixin.WorkflowSelector );

	bs.distributionConnector.workflows.trigger.TimeSMWProperty.prototype.getFields = function () {
		const editorDataValue = this.value.editorData || {};
		this.propertyInput = new bs.swmconnector.ui.SMWPropertyInputWidget( { required: true, value: editorDataValue.property || [] } );
		this.daysInput = new OO.ui.NumberInputWidget( { required: true, min: -1000, max: 1000, step: 1, value: editorDataValue.days || 0 } );

		return bs.distributionConnector.workflows.trigger.TimeSMWProperty.parent.prototype.getFields.call( this ).concat( [
			this.pickerLayout,
			new OO.ui.FieldLayout( this.propertyInput, {
				label: mw.message( 'bs-distributionconnector-workflows-ui-trigger-field-property' ).text(),
				align: 'top'
			} ),
			new OO.ui.FieldLayout( this.daysInput, {
				label: mw.message( 'bs-distributionconnector-workflows-ui-trigger-field-days' ).text(),
				align: 'top'
			} )
		] );
	};

	bs.distributionConnector.workflows.trigger.TimeSMWProperty.prototype.getValidity = function () {
		const dfd = $.Deferred();
		bs.distributionConnector.workflows.trigger.TimeSMWProperty.parent.prototype.getValidity.call( this )
			.done( () => {
				this.propertyInput.getValidity().done( () => {
					this.daysInput.getValidity().done( () => {
						dfd.resolve();
					} ).fail( () => {
						this.daysInput.setValidityFlag( false );
						dfd.reject();
					} );
				} ).fail( () => {
					this.propertyInput.setValidityFlag( false );
					dfd.reject();
				} );
			} ).fail( () => {
				dfd.reject();
			} );

		return dfd.promise();
	};

	bs.distributionConnector.workflows.trigger.TimeSMWProperty.prototype.generateData = function () {
		bs.distributionConnector.workflows.trigger.TimeSMWProperty.parent.prototype.generateData.call( this );
		this.value.rules.include.pages = this.generatePagesQuery();
		this.value.editorData = {
			property: this.propertyInput.getValue(),
			days: this.daysInput.getValue()
		};

		return this.value;
	};

	bs.distributionConnector.workflows.trigger.TimeSMWProperty.prototype.generatePagesQuery = function () {
		const days = this.daysInput.getValue(),
			sign = days >= 0 ? '+' : '',
			query = '[[{0}::>={{#time:Y-m-d|{1}{2} days}}]][[{0}::<{{#time:Y-m-d|{{#time:Y-m-d|{1}{2} days}}+1 days}}]]'
				.format( this.propertyInput.getValue(), sign, days ),
			params = [
				'format=plainlist',
				'limit=9999',
				'link=none',
				'sep={{!}}'
			];

		return '{{#ask:{0}|{1}}}'.format( query, params.join( '|' ) );
	};
}( mediaWiki, jQuery ) );
