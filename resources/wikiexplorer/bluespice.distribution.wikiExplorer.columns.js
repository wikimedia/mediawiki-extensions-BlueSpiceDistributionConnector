mw.hook( 'bs.wikiexplorer.oojs.columns' ).add( function( columns ) {
	columns.page_hits = {
		headerText: mw.message( 'bs-distributionconnector-hit-counter-wikiexplorer-column-name' ).text(),
		type: 'number',
		filter: {
			type: 'number'
		},
		sortable: true
	};
} );
