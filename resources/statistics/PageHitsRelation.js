( function ( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.distributionConnector.report' );

	bs.distributionConnector.report.PageHitsRelationReport = function ( cfg ) {
		bs.distributionConnector.report.PageHitsRelationReport.parent.call( this, cfg );
	};

	OO.inheritClass( bs.distributionConnector.report.PageHitsRelationReport, bs.aggregatedStatistics.report.ReportBase );

	bs.distributionConnector.report.PageHitsRelationReport.static.label =
		mw.message( 'bs-distributionconnector-statistics-report-page-hits-relation' ).text();

	bs.distributionConnector.report.PageHitsRelationReport.static.desc =
		mw.message( 'bs-distributionconnector-statistics-report-page-hits-relation-desc' ).text();

	bs.distributionConnector.report.PageHitsRelationReport.prototype.getFilters = function () {
		return [
			new bs.aggregatedStatistics.filter.IntervalFilter(),
			new bs.aggregatedStatistics.filter.PageFilter( { required: true } )
		];
	};

	bs.distributionConnector.report.PageHitsRelationReport.prototype.getChart = function () {
		return new bs.aggregatedStatistics.charts.LineChart();
	};

	bs.distributionConnector.report.PageHitsRelationReport.prototype.getAxisLabels = function () {
		return {
			value: mw.message( 'bs-distributionconnector-statistics-report-page-hits-relation-axis-label' ).text()
		};
	};

}( mediaWiki, jQuery, blueSpice ) );
