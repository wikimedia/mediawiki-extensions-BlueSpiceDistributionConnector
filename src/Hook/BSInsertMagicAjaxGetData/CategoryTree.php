<?php

namespace BlueSpice\DistributionConnector\Hook\BSInsertMagicAjaxGetData;

use BlueSpice\InsertMagic\Hook\BSInsertMagicAjaxGetData;

class CategoryTree extends BSInsertMagicAjaxGetData {

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
			'id' => 'categorytree',
			'type' => 'tag',
			'name' => 'categorytree',
			'desc' => $this->msg( 'bs-distributionconnector-tag-categorytree-desc' )->escaped(),
			'mwvecommand' => 'categoryTreeCommand',
			'code' => '<categorytree>Top_Level</categorytree>',
			'examples' => [
				[
					'code' => '<categorytree mode=pages>Manual</categorytree>'
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:CategoryTree'
		];

		return true;
	}

}
