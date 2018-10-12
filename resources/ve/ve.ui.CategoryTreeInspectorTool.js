ve.ui.CategoryTreeInspectorTool = function VeUiCategoryTreeInspectorTool( toolGroup, config ) {
	ve.ui.CategoryTreeInspectorTool.super.call( this, toolGroup, config );
};
OO.inheritClass( ve.ui.CategoryTreeInspectorTool, ve.ui.FragmentInspectorTool );
ve.ui.CategoryTreeInspectorTool.static.name = 'categoryTreeTool';
ve.ui.CategoryTreeInspectorTool.static.group = 'none';
ve.ui.CategoryTreeInspectorTool.static.autoAddToCatchall = false;
ve.ui.CategoryTreeInspectorTool.static.icon = 'markup';
ve.ui.CategoryTreeInspectorTool.static.title = OO.ui.deferMsg(
	'bs-distribution-ve-categorytree-title'
);
ve.ui.CategoryTreeInspectorTool.static.modelClasses = [ ve.dm.CategoryTreeNode ];
ve.ui.CategoryTreeInspectorTool.static.commandName = 'categoryTreeCommand';
ve.ui.toolFactory.register( ve.ui.CategoryTreeInspectorTool );

ve.ui.commandRegistry.register(
	new ve.ui.Command(
		'categoryTreeCommand', 'window', 'open',
		{ args: [ 'categoryTreeInspector' ], supportedSelections: [ 'linear' ] }
	)
);

