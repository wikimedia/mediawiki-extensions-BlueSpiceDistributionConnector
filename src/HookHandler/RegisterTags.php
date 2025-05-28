<?php

namespace BlueSpice\DistributionConnector\HookHandler;

use BlueSpice\DistributionConnector\Tag\UENoExport;
use MWStake\MediaWiki\Component\GenericTagHandler\Hook\MWStakeGenericTagHandlerInitTagsHook;

class RegisterTags implements MWStakeGenericTagHandlerInitTagsHook {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeGenericTagHandlerInitTags( array &$tags ) {
		$tags[] = new UENoExport();
	}
}
