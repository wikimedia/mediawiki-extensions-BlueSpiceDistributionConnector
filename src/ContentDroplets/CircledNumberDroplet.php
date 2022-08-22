<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use Message;
use RawMessage;

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
		return new RawMessage( 'Circled Number' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( 'Number in a circle with customizable background and foreground color' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return 'ext.bluespice.distribution.droplet.circlednumber';
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content' ];
	}
}
