<?php

namespace BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate;

use MediaWiki\Content\WikitextContent;
use MediaWiki\Maintenance\LoggedUpdateMaintenance;
use MediaWiki\MediaWikiServices;
use WikiPage;

require_once dirname( __DIR__, 5 ) . "/maintenance/Maintenance.php";

class AddVariablesPageProperties extends LoggedUpdateMaintenance {

	/**
	 * DB table where config values are stored.
	 *
	 * @var string
	 */
	private $configTable = 'bs_settings3';

	/**
	 * @inheritDoc
	 */
	public function doDBUpdates() {
		$this->output( "\nAdding 'variables' page properties...\n" );

		$dbr = $this->getDB( DB_REPLICA );
		$dbw = $this->getDB( DB_PRIMARY );
		$wikiPageQueryInfo = WikiPage::getQueryInfo();
		$wikiPageFactory = MediaWikiServices::getInstance()->getWikiPageFactory();

		$res = $dbr->newSelectQueryBuilder()
			->table( $wikiPageQueryInfo['tables'][0] )
			->fields( $wikiPageQueryInfo['fields'] )
			->where( [ 'page_content_model' => 'wikitext' ] )
			->caller( __METHOD__ )
			->fetchResultSet();

		foreach ( $res as $row ) {
			$wikiPage = $wikiPageFactory->newFromRow( $row );
			$content = $wikiPage->getContent();
			$text = ( $content instanceof WikitextContent ) ? $content->getText() : '';
			$regex = '/\{\{#var/';

			if ( preg_match( $regex, $text ) ) {
				$dbw->upsert(
					'page_props',
					[
						'pp_page' => $row->page_id,
						'pp_propname' => 'variables',
						'pp_value' => '1',
					],
					'pp_page',
					[ 'pp_value' => '1' ],
					__METHOD__
				);
			}
		}
		$this->output( "\nAdding 'variables' page properties...DONE\n" );

		return true;
	}

	/**
	 * @inheritDoc
	 */
	protected function getUpdateKey() {
		return 'variables_page_properties';
	}
}

$maintClass = AddVariablesPageProperties::class;
require_once RUN_MAINTENANCE_IF_MAIN;
