mw.hook( 'bs.wikiexplorer.oojs.columns' ).add( ( columns ) => {
	columns.page_hits = { // eslint-disable-line camelcase
		headerText: mw.message( 'bs-distributionconnector-hit-counter-wikiexplorer-column-name' ).text(),
		type: 'number',
		filter: {
			type: 'number'
		},
		sortable: true
	};
} );
