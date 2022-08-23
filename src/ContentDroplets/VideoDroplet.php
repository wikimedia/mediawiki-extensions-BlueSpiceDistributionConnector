<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\GenericDroplet;
use Message;
use RawMessage;

class VideoDroplet extends GenericDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return new RawMessage( 'Video' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( "Video description" );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'play';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return 'ext.bluespice.distribution.video.visualEditor';
	}

	/**
	 * @return array
	 */
	public function getCategories(): array {
		return [ 'media' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getContent(): string {
		return '<embedvideo service=youtube>https://www.youtube.com/watch?v=JvcmHfYAjMg</embedvideo>';
	}

	/**
	 * @inheritDoc
	 */
	public function getVeCommand(): ?string {
		return 'videoDropletTool';
	}
}
