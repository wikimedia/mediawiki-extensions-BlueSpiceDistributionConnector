<?php

namespace BlueSpice\DistributionConnector\ContentDroplets;

use MediaWiki\Extension\ContentDroplets\Droplet\TemplateDroplet;
use Message;
use RawMessage;

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
		return new RawMessage( 'ButtonLink' );
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): Message {
		return new RawMessage( 'Button with link' );
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
		return 'ext.bluespice.distribution.object.buttonlink';
	}

	/**
	 * @inheritDoc
	 */
	public function getCategories(): array {
		return [ 'content', 'navigation' ];
	}
}
