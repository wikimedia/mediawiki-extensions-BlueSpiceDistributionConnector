<?php

namespace BlueSpice\DistributionConnector\Tag;

use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\GenericTagHandler\ClientTagSpecification;
use MWStake\MediaWiki\Component\GenericTagHandler\GenericTag;
use MWStake\MediaWiki\Component\GenericTagHandler\ITagHandler;

class UENoExport extends GenericTag {

	/**
	 * @inheritDoc
	 */
	public function getTagNames(): array {
		return [ 'bs:uenoexport' ];
	}

	/**
	 * @return bool
	 */
	public function hasContent(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldParseInput(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getContainerElementName(): ?string {
		return 'div';
	}

	/**
	 * @inheritDoc
	 */
	public function getHandler( MediaWikiServices $services ): ITagHandler {
		return new UENoExportHandler();
	}

	/**
	 * @inheritDoc
	 */
	public function getParamDefinition(): ?array {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getClientTagSpecification(): ClientTagSpecification|null {
		return null;
	}
}
