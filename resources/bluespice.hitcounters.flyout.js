( function ( mw, $, bs, undefined ) {
	bs.util.registerNamespace( 'bs.hitcounters.flyout' );
	bs.hitcounters.flyout.makeItems = function () {
		if ( mw.config.get( 'bsgHitCountersSitetools' ) !== null ) {
			return {
				top: [
					Ext.create( 'BS.DistributionConnector.HitCounter.panel.HitCounters', {
						counts: mw.config.get( 'bsgHitCountersSitetools' ),
						text: mw.message( 'bs-distributionconnector-flyout-hitcounters-text' ).text()
					} )
				]
			}
		}

		return {};
	};

} )( mediaWiki, jQuery, blueSpice );
