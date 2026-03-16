<?php

// @codeCoverageIgnoreStart

use MediaWiki\MediaWikiServices;
use MediaWiki\User\UserFactory;
use Wikimedia\Rdbms\IMaintainableDatabase;

require_once dirname( __DIR__, 3 ) . '/maintenance/Maintenance.php';

// @codeCoverageIgnoreEnd
/**
 * .
 * ├── files.xml
 * └── import
 *     ├── ABC
 *     │   └── Test.png
 *     │       ├── 1.png
 *     │       ├── 2.png
 *     │       └── 3.png
 *     └── Test.png
 *         ├── 1.png
 *         ├── 2.png
 *         └── 3.png
 */
class ExportFiles extends Maintenance {

	private bool $overwrite = false;
	private bool $dryRun = false;
	private bool $verbose = false;
	private LocalRepo $localRepo;
	private UserFactory $userFactory;
	private IMaintainableDatabase $dbr;
	private string $basePath = '';

	/**
	 * Run like `php maintenance/exportFiles.php --dest=import/files.xml`
	 */
	public function __construct() {
		parent::__construct();
		$this->addOption( 'verbose', 'More verbose output' );
		$this->addOption( 'dest', 'Path to export XML', true );
	}

	/**
	 * @return void
	 */
	public function execute() {
		$this->verbose = $this->getOption( 'verbose', false );
		$exportXML = $this->getOption( 'dest' );
		$this->basePath = dirname( realpath( $exportXML ) ) . '/';

		$services = MediaWikiServices::getInstance();
		$this->localRepo = $services->getRepoGroup()->getLocalRepo();
		$this->userFactory = $services->getUserFactory();

		$dom = new DOMDocument();
		$dom->loadXML( '<mediawiki></mediawiki>' );

		$this->dbr = $this->getDB( DB_REPLICA );
		$filePages = $this->dbr->newSelectQueryBuilder()
			->select( [ 'page_id', 'page_title' ] )
			->from( 'page' )
			->where( [ 'page_namespace' => NS_FILE ] )
			->caller( __METHOD__ )
			->fetchResultSet();

		foreach ( $filePages as $filePage ) {

			$fileTitle = $filePage->page_title;

			$this->output( "Exporting file: $fileTitle\n" );

			$file = $this->localRepo->newFile( $fileTitle );
			if ( !$file->exists() ) {
				$this->output( "File $fileTitle does not exist, skipping export.\n" );
				continue;
			}

			$this->exportFile( $file, $dom );
		}

		$dom->formatOutput = true;
		$outfile = "{$this->basePath}/files.xml";
		$this->output( "Saving export to $outfile\n" );
		$dom->save( $outfile );
	}

	/**
	 * @param File $file
	 * @param DOMDocument $dom
	 * @return void
	 */
	private function exportFile( File $file, DOMDocument $dom ): void {
		$fileElement = $dom->createElement( 'file' );
		$titleEl = $dom->createElement( 'title', $file->getTitle()->getText() );
		$fileElement->appendChild( $titleEl );
		$dom->documentElement->appendChild( $fileElement );
		$fileName = $file->getName();
		// Potential Extension:NSFileRepo compatibility
		$fileName = str_replace( ':', '/', $fileName );
		$storagePath = $this->basePath . '/export/' . $fileName . '/';
		wfMkdirParents( $storagePath );

		$historicalRevisions = $file->getHistory();

		$allRevisions = array_merge( $historicalRevisions, [ $file ] );
		foreach ( $allRevisions as $idx => $fileRev ) {
			$timestamp = $fileRev->getTimestamp();
			$username = $fileRev->getUploader()?->getName() ?? 'unknown';
			/* TBD: Not simple to retrieve from OM */
			$comment = '';
			$localReference = $fileRev->getLocalRefPath();

			$revisionEl = $dom->createElement( 'revision' );
			$timestampEl = $dom->createElement( 'timestamp', wfTimestamp( TS_ISO_8601, $timestamp ) );
			$contributorEl = $dom->createElement( 'contributor' );
			$usernameEl = $dom->createElement( 'username', $username );
			$contributorEl->appendChild( $usernameEl );
			$commentEl = $dom->createElement( 'comment', $comment );
			$fileExtension = $fileRev->getExtension();
			$targetFileName = "$idx.$fileExtension";
			$exportPathName = "./export/$fileName/$targetFileName";
			copy( $localReference, $this->basePath . $exportPathName );
			$localReferenceEl = $dom->createElement( 'localReference', $exportPathName );

			$revisionEl->appendChild( $timestampEl );
			$revisionEl->appendChild( $contributorEl );
			$revisionEl->appendChild( $commentEl );
			$revisionEl->appendChild( $localReferenceEl );
			$fileElement->appendChild( $revisionEl );

			if ( $this->verbose ) {
				$this->output( "Exported revision from {$timestamp} by {$username}\n" );
			}
		}
	}

}

// @codeCoverageIgnoreStart
$maintClass = ExportFiles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
