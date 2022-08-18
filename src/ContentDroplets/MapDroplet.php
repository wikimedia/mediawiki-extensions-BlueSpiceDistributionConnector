<?php

declare( strict_types = 1 );

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use Message;
use RawMessage;

class MapDroplet extends TemplateDroplet {

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return new RawMessage( 'Map' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( "Map description" );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'map';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return 'ext.bluespice.distribution.droplet.map';
	}

	/**
	 * @return array
	 */
	public function getCategories(): array {
		return [ 'data', 'visualization' ];
	}

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getTarget(): string {
		return 'Map';
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return [
			'center' => ''
		];
	}

}
