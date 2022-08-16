<?php

declare( strict_types = 1 );

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TagDroplet;
use Message;
use RawMessage;

class GalleryDroplet extends TagDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return new RawMessage( 'Gallery' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( "Gallery description" );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'imageGallery';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return 'ext.visualEditor.mwgallery';
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
