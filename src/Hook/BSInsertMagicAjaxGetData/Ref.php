<?php

namespace BlueSpice\DistributionConnector\Hook\BSInsertMagicAjaxGetData;

use BlueSpice\InsertMagic\Hook\BSInsertMagicAjaxGetData;

class Ref extends BSInsertMagicAjaxGetData {

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
			'id' => 'ref',
			'type' => 'tag',
			'name' => 'ref',
			'desc' => $this->msg( 'bs-distributionconnector-tag-ref-desc' )->escaped(),
			'code' => '<ref>Footnote text</ref>',
			'examples' => [
				[
					'code' => $this->getCode(),
				]
			],
			'helplink' => 'https://en.wiki.bluespice.com/wiki/Reference:Cite'
		];

		return true;
	}

	/**
	 *
	 * @return string
	 */
	private function getCode() {
		return "Working with Wikis <ref>Wikis allow users not just to "
				. "read an article but also to edit</ref>is fun.\n"
				. "It is very useful to use footnotes <ref>A note can provide an author's "
				. "comments on the main text or citations of a reference work</ref> in "
				. "the articles.\n\n"
				. "==References==\n"
				. "<references/>";
	}

}
