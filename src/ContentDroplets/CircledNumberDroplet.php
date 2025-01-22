<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use MediaWiki\Message\Message;

class CircledNumberDroplet extends TemplateDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getTarget(): string {
		return 'CircledNumber';
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return [
			'bgColor' => 'red',
			'fgColor' => 'white',
			'number' => '1'
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'droplets-circled-number-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'droplets-circled-number-description' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-circled-number';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.bluespice.distribution.droplet.circlednumber' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content' ];
	}
}
