<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use Message;
use RawMessage;

class SubpagesDroplet extends TemplateDroplet {

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return new RawMessage( 'Subpages' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( "Subpages description" );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'listBullet';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return 'ext.bluespice.distribution.droplet.subpages';
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
