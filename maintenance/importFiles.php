<?php

// @codeCoverageIgnoreStart

use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MediaWiki\User\UserFactory;
use MediaWiki\User\UserRigorOptions;

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
class ImportFiles extends Maintenance {

	private bool $overwrite = false;
	private bool $dryRun = false;
	private bool $verbose = false;
	private LocalRepo $localRepo;
	private UserFactory $userFactory;
	private string $basePath = '';

	/**
	 * Run like `php maintenance/importFiles.php --src=import/files.xml`
	 */
	public function __construct() {
		parent::__construct();
		$this->addOption( 'overwrite', 'Overwrite existing files?' );
		$this->addOption( 'dry', 'Dry run. Do not actually upload files to the repo' );
		$this->addOption( 'verbose', 'More verbose output' );
		$this->addOption( 'src', 'Path to import XML', true );
	}

	/**
	 * @return void
	 */
	public function execute() {
		$this->overwrite = $this->getOption( 'overwrite', false );
		$this->dryRun = $this->getOption( 'dry', false );
		$this->verbose = $this->getOption( 'verbose', false );
		$importXML = $this->getOption( 'src' );
		$this->basePath = dirname( realpath( $importXML ) ) . '/';

		$services = MediaWikiServices::getInstance();
		$this->localRepo = $services->getRepoGroup()->getLocalRepo();
		$this->userFactory = $services->getUserFactory();

		$dom = new DOMDocument();
		$dom->load( $importXML );

		$fileElements = $dom->getElementsByTagName( 'file' );
		foreach ( $fileElements as $fileElement ) {
			$titleEl = $fileElement->getElementsByTagName( 'title' )->item( 0 );
			$fileTitle = $titleEl ? $titleEl->textContent : '';
			if ( !$fileTitle ) {
				$this->output( "File element missing title, skipping" );
				continue;
			}
			$this->output( "Processing file: $fileTitle\n" );
			$this->importFile( $fileElement, $fileTitle );
		}
	}

	private function importFile( DOMElement $fileElement, string $fileTitle ) {
		$latestFileRevision = [
			'timestamp' => '',
			'contributor' => '',
			'comment' => '',
			'data' => ''
		];
		$historicalFileRevisions = [];

		$revisionEls = $fileElement->getElementsByTagName( 'revision' );
		$latestTimestampSoFar = new DateTime( '1970-01-01T00:00:00Z' );
		foreach ( $revisionEls as $revisionEl ) {
			$timestampEl = $revisionEl->getElementsByTagName( 'timestamp' )->item( 0 );
			$timestamp = $timestampEl ? $timestampEl->textContent : '';

			$contributorEl = $revisionEl->getElementsByTagName( 'contributor' )->item( 0 );
			$usernameEl = $contributorEl ? $contributorEl->getElementsByTagName( 'username' )->item( 0 ) : null;
			$contributorUsername = $usernameEl ? $usernameEl->textContent : '';

			$commentEl = $revisionEl->getElementsByTagName( 'comment' )->item( 0 );
			$comment = $commentEl ? $commentEl->textContent : '';

			$dataEl = $revisionEl->getElementsByTagName( 'data' )->item( 0 );
			$data = $dataEl ? $dataEl->textContent : '';

			$fileRevision = [
				'timestamp' => wfTimestamp( TS_MW, $timestamp ),
				'contributor' => $contributorUsername,
				'comment' => $comment,
				'data' => $this->basePath . $data
			];

			$currentTimestamp = new DateTime( $timestamp );
			if ( $currentTimestamp > $latestTimestampSoFar ) {
				$latestTimestampSoFar = $currentTimestamp;
				$latestFileRevision = $fileRevision;
			} else {
				$historicalFileRevisions[] = $fileRevision;
			}
		}

		if ( $this->verbose ) {
			$this->output( "Latest revision:\n" );
			$this->output( "  Timestamp: " . $latestFileRevision['timestamp'] . "\n" );
			$this->output( "  Contributor: " . $latestFileRevision['contributor'] . "\n" );
			$this->output( "  Comment: " . $latestFileRevision['comment'] . "\n" );
			$this->output( "  Data: " . $latestFileRevision['data'] . "\n" );

			$this->output( "Historical revisions:\n" );
			foreach ( $historicalFileRevisions as $index => $historicalRevision ) {
				$this->output( "Revision " . ( $index + 1 ) . ":\n" );
				$this->output( "  Timestamp: " . $historicalRevision['timestamp'] . "\n" );
				$this->output( "  Contributor: " . $historicalRevision['contributor'] . "\n" );
				$this->output( "  Comment: " . $historicalRevision['comment'] . "\n" );
				$this->output( "  Data: " . $historicalRevision['data'] . "\n" );
			}
		}

		$file = $this->localRepo->newFile( $fileTitle );
		$fileExists = $file->exists();
		if ( !$this->overwrite && $fileExists ) {
			$this->output( "File $fileTitle already exists, skipping import.\n" );
			return;
		}

		$this->output( "Importing latest revision for file: $fileTitle\n" );
		if ( !$this->dryRun ) {
			$this->uploadLatestRevision( $file, $latestFileRevision );
		} else {
			$this->output( "Dry run enabled, not actually uploading file.\n" );
		}

		$latestRevisionTimestamp = $latestFileRevision['timestamp'];
		$preprocessedHistoricalFileRevisions = $this->calculateArchiveName(
			$historicalFileRevisions,
			$latestRevisionTimestamp,
			$fileTitle
		);

		foreach ( $preprocessedHistoricalFileRevisions as $historicalRevision ) {
			$this->output( "Importing historical revision: " . $historicalRevision['archive_name'] . "\n" );
			$file = $this->localRepo->newFromArchiveName( $fileTitle, $historicalRevision['timestamp'] );
			if ( !$this->dryRun ) {
				$this->uploadHistoricalRevision( $file, $historicalRevision );
			} else {
				$this->output( "Dry run enabled, not actually uploading file.\n" );
			}
		}
	}

	/**
	 * @param LocalFile $file
	 * @param array $revision
	 * @return void
	 */
	private function uploadLatestRevision( LocalFile $file, $revision ) {
		$user = $this->userFactory->newFromName( $revision['contributor'], UserRigorOptions::RIGOR_NONE );
		$status = $file->upload(
			$revision['data'],
			$revision['comment'],
			$revision['comment'],
			0,
			false,
			$revision['timestamp'],
			$user
		);

		if ( $status->isOK() ) {
			$this->output( "Successfully imported revision for file: " . $file->getName() . "\n" );
		} else {
			$this->output( "Failed to import revision for file: " . $file->getName() . "\n" );
			foreach ( $status->getMessages() as $msgSpec ) {
				$msg = Message::newFromSpecifier( $msgSpec );
				$this->output( "  * {$msg->plain()}\n" );
			}
		}
	}

	/**
	 * @param OldLocalFile $file
	 * @param array $revision
	 * @return void
	 */
	private function uploadHistoricalRevision( OldLocalFile $file, $revision ) {
		$user = $this->userFactory->newFromName( $revision['contributor'], UserRigorOptions::RIGOR_NONE );
		$status = $file->uploadOld(
			$revision['data'],
			$revision['timestamp'],
			$revision['comment'],
			$user
		);

		if ( $status->isOK() ) {
			$this->output( "Successfully imported revision for file: " . $file->getName() . "\n" );
		} else {
			$this->output( "Failed to import revision for file: " . $file->getName() . "\n" );
			foreach ( $status->getMessages() as $msgSpec ) {
				$msg = Message::newFromSpecifier( $msgSpec );
				$this->output( "  * {$msg->plain()}\n" );
			}
		}
	}

	/**
	 *
	 * @param array $historicalRevisions
	 * @param string $latestRevisionTimestamp
	 * @param string $fileTitle
	 * @return array
	 */
	private function calculateArchiveName( array $historicalRevisions, string $latestRevisionTimestamp, string $fileTitle ): array {
		usort( $historicalRevisions, static function ( $a, $b ) {
			return (int)$a['timestamp'] < (int)$b['timestamp'];
		} );

		// The "archive name" of each historical file revision
		// is the file title with the timestamp of the
		// _previous_ revision prepended and separated by an
		// exclamation mark.
		// Example: 20240621062010!Test_image.png
		$historicalRevisionsWithArchiveNames = [];
		$previousRevisionTimestamp = $latestRevisionTimestamp;
		foreach ( $historicalRevisions as $revision ) {
			$archiveName = $previousRevisionTimestamp . '!' . $fileTitle;
			$revision['archive_name'] = $archiveName;
			$historicalRevisionsWithArchiveNames[] = $revision;
			$previousRevisionTimestamp = $revision['timestamp'];

			if ( $this->verbose ) {
				$this->output( "  Revision timestamp: " . $revision['timestamp'] . "\n" );
				$this->output( "  -> Archive name: " . $revision['archive_name'] . "\n" );
			}
		}
		return $historicalRevisionsWithArchiveNames;
	}

}

// @codeCoverageIgnoreStart
$maintClass = ImportFiles::class;
require_once RUN_MAINTENANCE_IF_MAIN;
// @codeCoverageIgnoreEnd
