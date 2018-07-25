Ext.onReady( function () {
	Ext.Loader.setPath(
			'BS.HitCounters',
			"/bluespice/extensions/BlueSpiceDistributionConnector" +
			//not set? 
			//
			'/resources/BS.HitCounters'
			);
	console.log( bs.em.paths.get( 'BlueSpiceDistributionConnector' ) );
} );
( function ( mw, $, bs, undefined ) {
	bs.util.registerNamespace( 'bs.hitcounters.flyout' );
	bs.hitcounters.flyout.makeItems = function () {
		if ( mw.config.get( 'bsgHitCountersSitetools' ) !== null ) {
			return {
				top: [
					Ext.create( 'BS.HitCounters.panel.HitCounters', {
						counts: mw.config.get( 'bsgHitCountersSitetools' ),
						text: mw.message( 'bs-distribution-flyout-hitcounters-text' ).text()
					} )
				]
			}
		}

		return {};
	};

} )( mediaWiki, jQuery, blueSpice );
console.log( mw.message( 'bs-distribution-flyout-hitcounters-text' ).text() );