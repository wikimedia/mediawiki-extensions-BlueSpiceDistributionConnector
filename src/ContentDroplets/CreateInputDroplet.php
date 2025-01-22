<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use MediaWiki\Message\Message;

class CreateInputDroplet extends TemplateDroplet {

	/**
	 */
	public function __construct() {
	}

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getTarget(): string {
		return 'CreateInput';
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return [
			'alignment' => 'center',
			'buttonlabel' => 'Create',
			'preload' => '',
			'placeholder' => 'Enter page name',
			'prefix' => ''
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'droplets-create-input-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'droplets-create-input-desc' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-create-input';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.bluespice.distribution.droplet.createInput' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content', 'navigation' ];
	}
}
