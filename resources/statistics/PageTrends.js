( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.report' );

	bs.distributionConnector.report.PageTrendsReport = function ( cfg ) {
		bs.distributionConnector.report.PageTrendsReport.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.report.PageTrendsReport, bs.distributionConnector.report.PageHitsReport );

	bs.distributionConnector.report.PageTrendsReport.static.label = mw.message( 'bs-distributionconnector-statistics-report-page-trends' ).text();

	bs.distributionConnector.report.PageTrendsReport.prototype.getAxisLabels = function () {
		return {
			value: mw.message( 'bs-distributionconnector-statistics-report-page-trends-axis-value' ).text()
		};
	};

}( mediaWiki, jQuery, blueSpice ) );
