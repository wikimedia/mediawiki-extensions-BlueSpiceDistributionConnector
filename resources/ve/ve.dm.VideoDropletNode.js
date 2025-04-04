ve.dm.VideoDropletNode = function VeDmVideoDropletNode() {
	// Parent constructor
	ve.dm.VideoDropletNode.super.apply( this, arguments );
};

/* Inheritance */
OO.inheritClass( ve.dm.VideoDropletNode, ve.dm.MWInlineExtensionNode );

/* Static members */
ve.dm.VideoDropletNode.static.name = 'embedvideo';

ve.dm.VideoDropletNode.static.tagName = 'embedvideo';

// Name of the parser tag
ve.dm.VideoDropletNode.static.extensionName = 'embedvideo';

// This tag renders without content
ve.dm.VideoDropletNode.static.childNodeTypes = [];
ve.dm.VideoDropletNode.static.isContent = true;

/* Registration */
ve.dm.modelRegistry.register( ve.dm.VideoDropletNode );
