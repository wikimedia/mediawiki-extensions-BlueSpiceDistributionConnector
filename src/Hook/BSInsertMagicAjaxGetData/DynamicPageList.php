<?php

namespace BlueSpice\DistributionConnector\Hook\BSInsertMagicAjaxGetData;

use BlueSpice\InsertMagic\Hook\BSInsertMagicAjaxGetData;

class DynamicPageList extends BSInsertMagicAjaxGetData {

	/**
	 *
	 * @return bool
	 */
	protected function skipProcessing() {
		return $this->type !== 'tags';
	}

	/**
	 *
	 * @return bool
	 */
	protected function doProcess() {
		$this->response->result[] = (object)[
			'id' => 'dynamicpagelist',
			'type' => 'tag',
			'name' => 'dynamicpagelist',
			'desc' => $this->msg( 'bs-distributionconnector-tag-dynamicpagelist-desc' )->escaped(),
			'code' => "<DynamicPageList>\ncategory = Demo\n</DynamicPageList>",
			'examples' => [
				[
					'code' => $this->getCode(),
				]
			],
			'helplink' => 'https://www.mediawiki.org/wiki/Extension:DynamicPageList_%28Wikimedia%29#Use'
		];

		return true;
	}

	/**
	 *
	 * @return string
	 */
	private function getCode() {
		return <<<EOT
"<DynamicPageList>
category = Pages recently transferred from Meta
count = 5
order = ascending
addfirstcategorydate = true
</DynamicPageList>"
EOT;
	}

}
