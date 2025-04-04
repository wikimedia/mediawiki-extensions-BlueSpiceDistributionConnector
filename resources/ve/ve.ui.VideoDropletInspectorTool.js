ve.ui.VideoDropletInspectorTool = function VeUiVideoDropletInspectorTool( toolGroup, config ) {
	ve.ui.VideoDropletInspectorTool.super.call( this, toolGroup, config );
};
OO.inheritClass( ve.ui.VideoDropletInspectorTool, ve.ui.FragmentInspectorTool );
ve.ui.VideoDropletInspectorTool.static.name = 'embedvideo';
ve.ui.VideoDropletInspectorTool.static.group = 'none';
ve.ui.VideoDropletInspectorTool.static.autoAddToCatchall = false;
ve.ui.VideoDropletInspectorTool.static.icon = 'play';
ve.ui.VideoDropletInspectorTool.static.title =
	mw.message( 'bs-distributionconnector-videodropletinspector-title' ).text();
ve.ui.VideoDropletInspectorTool.static.modelClasses = [ ve.dm.VideoDropletNode ];
ve.ui.VideoDropletInspectorTool.static.commandName = 'videoDropletTool';
ve.ui.toolFactory.register( ve.ui.VideoDropletInspectorTool );

ve.ui.commandRegistry.register(
	new ve.ui.Command(
		'videoDropletTool', 'window', 'open',
		{ args: [ 'videoDropletInspector' ], supportedSelections: [ 'linear' ] }
	)
);
