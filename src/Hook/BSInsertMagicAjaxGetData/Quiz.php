<?php

namespace BlueSpice\DistributionConnector\Hook\BSInsertMagicAjaxGetData;

use BlueSpice\InsertMagic\Hook\BSInsertMagicAjaxGetData;

class Quiz extends BSInsertMagicAjaxGetData {

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
			'id' => 'quiz',
			'type' => 'tag',
			'name' => 'quiz',
			'desc' => $this->msg( 'bs-distributionconnector-tag-quiz-desc' )->escaped(),
			'code' => "<quiz>\n{ Your question }\n+ correct answer\n- incorrect answer\n</quiz>",
			'examples' => [
				[
					'code' => $this->getCode(),
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:Quiz'
		];

		return true;
	}

	/**
	 *
	 * @return string
	 */
	private function getCode() {
		return "<quiz>\n{ Your question }\n+ correct answer\n- incorrect answer\n</quiz>";
	}

}
