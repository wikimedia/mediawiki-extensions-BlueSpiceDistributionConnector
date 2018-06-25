ve.dm.CategoryTreeNode = function VeDmCategoryTreeNode() {
	// Parent constructor
	ve.dm.CategoryTreeNode.super.apply( this, arguments );
};

/* Inheritance */

OO.inheritClass( ve.dm.CategoryTreeNode, ve.dm.MWInlineExtensionNode );

/* Static members */

ve.dm.CategoryTreeNode.static.name = 'categorytree';

ve.dm.CategoryTreeNode.static.tagName = 'categorytree';

// Name of the parser tag
ve.dm.CategoryTreeNode.static.extensionName = 'categorytree';

// This tag renders without content
ve.dm.CategoryTreeNode.static.childNodeTypes = [];
ve.dm.CategoryTreeNode.static.isContent = true;

/* Registration */

ve.dm.modelRegistry.register( ve.dm.CategoryTreeNode );
