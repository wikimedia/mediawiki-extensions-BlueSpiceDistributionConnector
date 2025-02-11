<?php

use BlueSpice\DistributionConnector\ContentFetch\ExportListParser;
use BlueSpice\DistributionConnector\ContentFetch\SourceWikiReader;
use MediaWiki\Json\FormatJson;
use MediaWiki\Maintenance\Maintenance;
use MediaWiki\Status\Status;

require_once dirname( dirname( dirname( __DIR__ ) ) ) . "/maintenance/Maintenance.php";

class BSFetchContents extends Maintenance {

	/**
	 * @var SourceWikiReader
	 */
	private $wikiReader;

	/**
	 * @var ExportListParser
	 */
	private $exportListParser;

	/**
	 * @var string
	 */
	private $sourcePage;

	/**
	 * @var string
	 */
	private $targetPath;

	public function __construct() {
		parent::__construct();

		$this->addDescription( 'Fetch default wiki pages (like default templates) from specified wiki page. These wiki pages then will be imported with "update.php"' );
		$this->addOption( 'source', 'Source page to get list of templates to export from. Example: "MediaWiki:ExportList/BlueSpicePageTemplates"', true, true );
		$this->addOption( 'target', 'Target location to save list and content of wiki pages. Default: "extensions/BlueSpiceDistributionConnector/data/Content"', true, true );
		$this->addOption( 'endpoint', 'API endpoint of specified wiki. Example: "https://somewiki/api.php"', true, true );
		$this->addOption( 'username', 'Name of user, who is able to read content of source wiki page', true, true );
		$this->addOption( 'password', 'Password of user, who is able to read content of source wiki page', true, true );
	}

	public function execute() {
		// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
		global $IP;

		$this->sourcePage = $this->getOption( 'source' );
		// Normalize provided page name
		$this->sourcePage = str_replace( ' ', '_', $this->sourcePage );
		$this->targetPath = $this->getOption( 'target' );

		$this->targetPath = $IP . '/' . $this->targetPath;

		$endPoint = $this->getOption( 'endpoint' );

		$this->wikiReader = new SourceWikiReader( $endPoint );
		$this->exportListParser = new ExportListParser();

		if ( !file_exists( $this->targetPath . '/pages' ) ) {
			wfMkdirParents( $this->targetPath . '/pages' );
		}

		// Get login token
		$loginToken = $this->wikiReader->getLoginToken();

		$login = $this->getOption( 'username' );
		$password = $this->getOption( 'password' );

		// Log in
		$status = $this->wikiReader->login( $loginToken, $login, $password );
		if ( !$status->isGood() ) {
			$this->output( "Failed to log in.\n" );
			$this->outputErrors( $status );
			return;
		}

		$this->output( "Logged in as '{$login}'\n" );
		$this->output( "Reading contents from page '{$this->sourcePage}'...\n" );

		// Get content of the page with "export list" of necessary templates
		$content = $this->readExportList();
		if ( $content === '' ) {
			$this->output( "Failed to get \"export list\" page content.\n" );
			return;
		}

		$pagesList = $this->exportListParser->parse( $content );

		$pagesTitles = array_keys( $pagesList );

		// TODO: Split pages list to 50 titles per request
		$status = $this->wikiReader->readPagesContent( $pagesTitles );
		if ( !$status->isOK() ) {
			$this->outputErrors( $status );
			return;
		}

		// If status is "ok", but there are still some errors - probably some pages are missing
		if ( $status->getErrors() ) {
			$this->outputErrors( $status );
		}

		$pagesContents = $status->getValue();
		foreach ( $pagesContents as $pageTitle => $pagesContent ) {
			$this->output( "Got content for page: \"$pageTitle\"\n" );
		}

		// Put pages contents to files
		foreach ( $pagesContents as $title => $pageContent ) {
			$title = str_replace( ' ', '_', $title );
			$nsTitle = '(Pages)';
			$pageTitle = $title;
			$titleParts = explode( ':', $title, 2 );
			if ( count( $titleParts ) === 2 ) {
				$nsTitle = $titleParts[0];
				$pageTitle = $titleParts[1];
			}

			$fileName = $this->makeFilename( $pageTitle );

			$pageContentDir = $this->targetPath . '/pages/' . $nsTitle;
			$pageContentPath = $pageContentDir . '/' . $fileName;

			// We should save path to wiki content relative to manifest file
			$relativePageContentPath = '/pages/' . $nsTitle . '/' . $fileName;

			wfMkdirParents( $pageContentDir );

			file_put_contents( $pageContentPath, $pageContent );

			$pagesList[$title]['content_path'] = $relativePageContentPath;
			$pagesList[$title]['sha1'] = sha1_file( $pageContentPath );

			// "old_sha1" field will be used to check if page was updated by script
			// (we'll know that because we have SHA1 for each page version provided)

			// If current SHA1 of page content will differ from each of SHA1 in "old_sha1" -
			// then page was changed by user, not by script
			$pagesList[$title]['old_sha1'] = [];
		}

		// If we failed to get content for some pages - we don't need to save any information about them
		// or process them.
		// The main point is to import page content, so we cannot proceed without content.
		$pagesList = array_filter( $pagesList, static function ( $value ) {
			return isset( $value['sha1'] );
		} );

		// Create/rewrite manifest file
		$manifestPath = $this->updateManifest( $pagesList );

		$this->output( "Manifest file created: $manifestPath\n" );
	}

	/**
	 * Makes correct filename from wiki page title.
	 * This file is further used to store wiki page content (wikitext).
	 *
	 * @param string $name
	 * @return string
	 */
	private function makeFilename( string $name ): string {
		$name = str_replace( ' ', '_', $name );
		$illegalFilenameSymbols = [ '/', '\\', '<', '>', '?', ':', '*', '"', '|', ';', '!' ];
		$filename = str_replace( $illegalFilenameSymbols, '', $name );

		return $filename . '.wiki';
	}

	/**
	 * Outputs errors from specified status in current output (usually CLI)
	 *
	 * @param Status $status
	 * @return void
	 */
	private function outputErrors( Status $status ): void {
		foreach ( $status->getErrors() as $error ) {
			$this->output( var_export( $error, true ) );
			$errorType = ucfirst( $error['type'] );
			$this->output( "$errorType:\n" );
			$errorMessages = $error['message'];
			if ( !is_array( $errorMessages ) ) {
				$errorMessages = [ $errorMessages ];
			}
			foreach ( $errorMessages as $message ) {
				$this->output( "\t$message\n" );
			}
		}
	}

	/**
	 * Reads "export list" wiki page from the source wiki.
	 * This page contains UI-friendly formatted list of wiki pages which need to be exported.
	 * This list should be parsed after that.
	 *
	 * @return string
	 * @see BSFetchContents::parseExportList()
	 */
	private function readExportList(): string {
		$status = $this->wikiReader->readPagesContent( [ $this->sourcePage ] );

		if ( $status->isGood() ) {
			$content = $status->getValue();
		} else {
			$this->outputErrors( $status );
			return '';
		}

		return $content[$this->sourcePage];
	}

	/**
	 * Creates "manifest.json" file, which contains list of BlueSpice pages prepared for import.
	 * Among different information about page, its SHA1 hash (hash of content) is persisted too.
	 *
	 * If manifest file already exists from previous content fetching iterations -
	 * - then it'll be rewritten, but with persisting previous SHA1 hashes.
	 * We'll need all content hashes afterwards, on the import step, to indicate if page was changed by user.
	 *
	 * All other information (like label or title) depends only on current page version,
	 * so it'll be completely replaced.
	 *
	 * Manifest structure - such JSON-encoded array:
	 * [
	 * 		page_title1 => [
	 * 			'lang' => lang1
	 * 			'label' => label1
	 * 			'description' => description1
	 * 			'target_title' => target_title1,
	 * 			'content_path => content_path1,
	 * 			'sha1' => sha1_1,
	 * 			'old_sha1' => [ old_sha1_1, old_sha1_2, ... ]
	 * 		],
	 * 		page_title2 => [
	 * 		...
	 * 		],
	 * 		...
	 * ]
	 *
	 * @param array $pagesList Array with necessary information about wiki pages
	 * @return string Path to manifest file
	 */
	private function updateManifest( array $pagesList ): string {
		$manifestPath = $this->targetPath . '/manifest.json';
		if ( file_exists( $manifestPath ) ) {
			$prevPagesList = FormatJson::decode( file_get_contents( $manifestPath ), true );

			foreach ( $pagesList as $pageTitle => $pageData ) {
				// If specified title exists in previous manifest
				if ( isset( $prevPagesList[$pageTitle] ) ) {
					// If there are already some old SHA1 persisted - copy them
					if ( isset( $prevPagesList[$pageTitle]['old_sha1'] ) ) {
						$pagesList[$pageTitle]['old_sha1'] = $prevPagesList[$pageTitle]['old_sha1'];
					}

					// If page content changed - then save SHA1 of previous content
					if ( $prevPagesList[$pageTitle]['sha1'] !== $pageData['sha1'] ) {
						$pagesList[$pageTitle]['old_sha1'][] = $prevPagesList[$pageTitle]['sha1'];
					}
				}
			}
		}

		$manifestContent = FormatJson::encode( $pagesList, true );
		// Alter JSON formatting to make it comply to the CI/CD pipeline requirements
		$manifestContent = str_replace( '    ', "\t", $manifestContent );
		$manifestContent .= "\n";
		file_put_contents( $manifestPath, $manifestContent );

		return $manifestPath;
	}
}

$maintClass = BSFetchContents::class;
require_once RUN_MAINTENANCE_IF_MAIN;
