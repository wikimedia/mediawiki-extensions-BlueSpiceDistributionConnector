Ext.onReady( function () {
	Ext.Loader.setPath(
			'BS.HitCounters',
			bs.em.paths.get( 'BlueSpiceDistributionConnector' ) +
			'/resources/BS.HitCounters'
			);
	
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
