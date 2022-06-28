<?php

namespace BlueSpice\DistributionConnector\Maintenance\PostDatabaseUpdate;

use BlueSpice\DistributionConnector\ContentImport\ImportLanguage;
use CommentStoreComment;
use ExtensionRegistry;
use Maintenance;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MWContentSerializationException;
use MWException;
use TextContent;
use Title;
use User;
use WikiPage;

class ImportDefaultPages extends Maintenance {

	/**
	 * @var MediaWikiServices
	 */
	private $services;

	/**
	 * Code of language which is used to import.
	 *
	 * @var string
	 */
	private $importLanguageCode;

	/**
	 * User which is used to edit pages content
	 *
	 * @var User
	 */
	private $maintenanceUser;

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$this->services = MediaWikiServices::getInstance();

		$this->maintenanceUser = User::newSystemUser( 'BSMaintenance' );

		$this->output( "...ImportDefaultPages:\n" );

		$wikiLang = $this->services->getContentLanguage();
		$languageFallback = $this->services->getLanguageFallback();

		$importLanguage = new ImportLanguage( $languageFallback, $wikiLang->getCode() );
		$this->importLanguageCode = $importLanguage->getImportLanguage();

		$this->output( "...Language to import content: {$this->importLanguageCode}\n" );

		$this->importPages();

		return true;
	}

	private function importPages(): void {
		// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
		global $IP;

		$attrName = 'BlueSpiceDistributionConnectorContentManifests';
		$manifestsList = ExtensionRegistry::getInstance()->getAttribute( $attrName );

		if ( $manifestsList ) {
			$this->output( "...Import of default BlueSpice pages started...\n" );
			foreach ( $manifestsList as $manifestPath ) {
				$absoluteManifestPath = $IP . '/' . $manifestPath;
				if ( file_exists( $absoluteManifestPath ) ) {
					$this->output( "...Processing manifest file: '$absoluteManifestPath' ...\n" );
					$this->processManifestFile( $absoluteManifestPath );
				} else {
					$this->output( "...Manifest file does not exist: '$absoluteManifestPath'\n" );
				}
			}
		} else {
			$this->output( "No manifests to import..." );
		}
	}

	/**
	 * @param string $manifestPath
	 * @return void
	 * @throws MWException
	 * @throws MWContentSerializationException
	 */
	private function processManifestFile( string $manifestPath ): void {
		$pagesList = json_decode( file_get_contents( $manifestPath ), true );
		foreach ( $pagesList as $pageTitle => $pageData ) {
			$this->output( "... Processing page: $pageTitle\n" );

			if ( $pageData['lang'] !== $this->importLanguageCode ) {
				$this->output( "... Wrong page language. Skipping...\n" );
				continue;
			}

			if ( !isset( $pageData['sha1'] ) || !isset( $pageData['content_path'] ) ) {
				$this->output( "Wikitext content is not available!\n" );
				continue;
			}

			$targetTitle = $pageData['target_title'];

			$titleFactory = $this->services->getTitleFactory();
			$title = $titleFactory->newFromText( $targetTitle, NS_MAIN );

			$pageContentPath = dirname( $manifestPath ) . $pageData['content_path'];

			if ( !$title->exists( Title::READ_LATEST ) ) {
				$this->output( "...Creating page '{$title->getPrefixedDBkey()}'...\n" );

				$this->importWikiContent( $title, $pageContentPath );
			} else {
				$currentHash = $this->getContentHash( $title );

				// If hashes are equal - then this page is exactly in the same state in which it was delivered
				if ( $currentHash === $pageData['sha1'] ) {
					// Currently nothing to do here
				} else {
					// If hashes differ - then this page either has old content or was touched by user.
					// So we'll check if current content hash equals one of the old hashes of page content.
					// If current hash equals one of the old ones - then page just has old content.
					// So we can safely update its content.

					// In other case page probably was touched by user, so we should do nothing without prompt.
					$changedByUser = true;

					$oldHashes = $pageData['old_sha1'];
					foreach ( $oldHashes as $hash ) {
						if ( $currentHash === $hash ) {
							$changedByUser = false;
							break;
						}
					}

					if ( !$changedByUser ) {
						// Page content is just outdated, so update it
						$this->output( "Wiki page already exists, but it has outdated content.\n" );
						$this->output( "...Updating page '{$title->getPrefixedDBkey()}'...\n" );

						$this->importWikiContent( $title, $pageContentPath );
					} else {
						// User did some changes to the page, do nothing for now
						$this->output( "Wiki page already exists, but it was changed by user! Skipping...\n" );
					}
				}
			}
		}
	}

	/**
	 * @param Title $title Target title, which should be imported
	 * @param string $contentPath Path to the page content, retrieved from manifest
	 * @return void
	 * @throws MWException
	 * @throws MWContentSerializationException
	 */
	private function importWikiContent( Title $title, string $contentPath ): void {
		$pageContent = file_get_contents( $contentPath );
		if ( !$pageContent ) {
			$this->output( "Failed to retrieve page content!" );
			return;
		}

		$wikiPage = WikiPage::factory( $title );
		$content = $wikiPage->getContentHandler()->makeContent( $pageContent, $title );

		$comment = CommentStoreComment::newUnsavedComment( 'Autogenerated' );

		$updater = $wikiPage->newPageUpdater( $this->maintenanceUser );
		$updater->setContent( SlotRecord::MAIN, $content );
		$newRevision = $updater->saveRevision( $comment );
		if ( $newRevision instanceof RevisionRecord ) {
			$this->output( "done.\n" );
		} else {
			$this->output( "error.\n" );
		}
	}

	/**
	 * Gets SHA1-hash of the latest revision content of specified title
	 *
	 * @param Title $title Processing title
	 * @return string SHA1-hash of page's the latest revision content,
	 * 		or empty string if content was not recognized
	 * @throws MWException
	 */
	private function getContentHash( Title $title ): string {
		$wikiPage = WikiPage::factory( $title );

		$updater = $wikiPage->newPageUpdater( $this->maintenanceUser );

		$parentRevision = $updater->grabParentRevision();
		$content = $parentRevision->getContent( SlotRecord::MAIN );
		if ( $content instanceof TextContent ) {
			$text = $content->getText();

			return sha1( $text );
		}

		return '';
	}

	/**
	 * @inheritDoc
	 */
	protected function getUpdateKey() {
		return 'ImportDefaultPages';
	}
}
