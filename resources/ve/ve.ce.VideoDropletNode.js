ve.ce.VideoDropletNode = function VeCeVideoDropletNode() {
	// Parent constructor
	ve.ce.VideoDropletNode.super.apply( this, arguments );
};

/* Inheritance */

OO.inheritClass( ve.ce.VideoDropletNode, ve.ce.MWInlineExtensionNode );

/* Static properties */
ve.ce.VideoDropletNode.static.name = 'embedvideo';

ve.ce.VideoDropletNode.static.primaryCommandName = 'embedvideo';

// If body is empty, tag does not render anything
ve.ce.VideoDropletNode.static.rendersEmpty = false;

/**
 * @inheritdoc
 */
ve.ce.VideoDropletNode.prototype.onSetup = function () {
	// Parent method
	ve.ce.VideoDropletNode.super.prototype.onSetup.call( this );
};

/**
 * @inheritdoc ve.ce.GeneratedContentNode
 */
ve.ce.VideoDropletNode.prototype.validateGeneratedContents = function ( $element ) {
	if ( $element.is( 'div' ) && $element.hasClass( 'errorbox' ) ) {
		return false;
	}
	return true;
};

/* Registration */
ve.ce.nodeFactory.register( ve.ce.VideoDropletNode );
