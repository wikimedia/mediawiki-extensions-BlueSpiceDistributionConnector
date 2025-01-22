<?php

declare( strict_types = 1 );

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TagDroplet;
use MediaWiki\Message\Message;

class GalleryDroplet extends TagDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'droplets-gallery-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'droplets-gallery-description' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-gallery';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.visualEditor.mwgallery', 'ext.bluespice.distribution.droplet.gallery' ];
	}

	/**
	 * @return array
	 */
	public function getCategories(): array {
		return [ 'media' ];
	}

	/**
	 *
	 * @return string
	 */
	protected function getTagName(): string {
		return 'gallery';
	}

	/**
	 * @return array
	 */
	protected function getAttributes(): array {
		return [];
	}

	/**
	 * @return bool
	 */
	protected function hasContent(): bool {
		return true;
	}

	/**
	 * @return string|null
	 */
	public function getVeCommand(): ?string {
		return 'gallery';
	}
}
