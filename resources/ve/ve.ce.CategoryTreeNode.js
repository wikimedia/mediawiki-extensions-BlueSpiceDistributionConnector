ve.ce.CategoryTreeNode = function VeCeCategoryTreeNode() {
	// Parent constructor
	ve.ce.CategoryTreeNode.super.apply( this, arguments );
};

/* Inheritance */

OO.inheritClass( ve.ce.CategoryTreeNode, ve.ce.MWInlineExtensionNode );

/* Static properties */

ve.ce.CategoryTreeNode.static.name = 'categorytree';

ve.ce.CategoryTreeNode.static.primaryCommandName = 'categorytree';

// If body is empty, tag does not render anything
ve.ce.CategoryTreeNode.static.rendersEmpty = false;

/**
 * @inheritdoc
 */
ve.ce.CategoryTreeNode.prototype.onSetup = function () {
	// Parent method
	ve.ce.CategoryTreeNode.super.prototype.onSetup.call( this );

	// DOM changes
	this.$element.addClass( 've-ce-categorytreenode' );
};

/**
 * @inheritdoc ve.ce.GeneratedContentNode
 */
ve.ce.CategoryTreeNode.prototype.validateGeneratedContents = function ( $element ) {
	if ( $element.is( 'div' ) && $element.hasClass( 'errorbox' ) ) {
		return false;
	}
	return true;
};

/* Registration */

ve.ce.nodeFactory.register( ve.ce.CategoryTreeNode );
