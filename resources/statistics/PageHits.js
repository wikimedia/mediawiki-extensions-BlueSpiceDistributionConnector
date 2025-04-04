( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.report' );

	bs.distributionConnector.report.PageHitsReport = function ( cfg ) {
		bs.distributionConnector.report.PageHitsReport.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.report.PageHitsReport, bs.aggregatedStatistics.report.ReportBase );

	bs.distributionConnector.report.PageHitsReport.static.label = mw.message( 'bs-distributionconnector-statistics-report-page-hits' ).text();

	bs.distributionConnector.report.PageHitsReport.prototype.getFilters = function () {
		return [
			new bs.aggregatedStatistics.filter.IntervalFilter(),
			new bs.aggregatedStatistics.filter.PageFilter( { required: true } )
		];
	};

	bs.distributionConnector.report.PageHitsReport.prototype.getChart = function () {
		return new bs.aggregatedStatistics.charts.LineChart();
	};

	bs.distributionConnector.report.PageHitsReport.prototype.getAxisLabels = function () {
		return {
			value: mw.message( 'bs-distributionconnector-statistics-report-page-hits-axis-label' ).text()
		};
	};

}( mediaWiki, jQuery, blueSpice ) );
