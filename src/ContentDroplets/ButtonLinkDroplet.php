<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use MediaWiki\Message\Message;

class ButtonLinkDroplet extends TemplateDroplet {

	/**
	 * Get target for the template
	 * @return string
	 */
	protected function getTarget(): string {
		return 'ButtonLink';
	}

	/**
	 * Template params
	 * @return array
	 */
	protected function getParams(): array {
		return [
			'external' => 'no',
			'target' => '',
			'label' => 'Button label',
			'format' => ''
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): Message {
		return Message::newFromKey( 'droplets-buttonlink-name' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return Message::newFromKey( 'droplets-buttonlink-desc' );
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return 'droplet-button';
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModules(): array {
		return [ 'ext.bluespice.distribution.object.buttonlink' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content', 'navigation' ];
	}
}
