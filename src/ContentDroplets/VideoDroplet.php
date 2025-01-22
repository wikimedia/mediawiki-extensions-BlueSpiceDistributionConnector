<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\GenericDroplet;
use MediaWiki\Message\Message;

class VideoDroplet extends GenericDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'droplets-video-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'droplets-video-description' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-video';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.bluespice.distribution.video.visualEditor' ];
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
