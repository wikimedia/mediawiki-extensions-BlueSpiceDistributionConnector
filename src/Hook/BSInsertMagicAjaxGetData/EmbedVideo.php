<?php

namespace BlueSpice\DistributionConnector\Hook\BSInsertMagicAjaxGetData;

use BlueSpice\InsertMagic\Hook\BSInsertMagicAjaxGetData;

class EmbedVideo extends BSInsertMagicAjaxGetData {

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
			'id' => 'embedvideo',
			'type' => 'tag',
			'name' => 'embedvideo',
			'desc' => $this->msg( 'bs-distributionconnector-tag-embedvideo-desc' )->escaped(),
			'code' => '<embedvideo service="supported service">Link to video</embedvideo>',
			'examples' => [
				[
					'code' => $this->getCode(),
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:EmbedVideo'
		];

		return true;
	}

	/**
	 *
	 * @return string
	 */
	private function getCode() {
		return "<embedvideo service=\"youtube\">"
				. "https://www.youtube.com/watch?v=o3wZxqPZxyo"
				. "</embedvideo>";
	}

}
