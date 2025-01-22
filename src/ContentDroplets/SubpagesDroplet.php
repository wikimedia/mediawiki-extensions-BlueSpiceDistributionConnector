<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use MediaWiki\Message\Message;

class SubpagesDroplet extends TemplateDroplet {

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'droplets-subpages-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'droplets-subpages-description' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-subpages';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.bluespice.distribution.droplet.subpages' ];
	}

	/**
	 * @return array
	 */
	public function getCategories(): array {
		return [ 'lists', 'navigation' ];
	}

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getTarget(): string {
		return 'Subpages';
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return [
			'parentnamespace' => '',
			'parentpage' => '',
			'cols' => 'no',
			'bullets' => 'no'
		];
	}

}
