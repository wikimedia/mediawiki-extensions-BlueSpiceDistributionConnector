<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Processor;

use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerAttachmentFinder;
use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFinder;
use MediaWiki\Config\Config;
use MediaWiki\Extension\PDFCreator\Processor\ImageProcessor;
use MediaWiki\Extension\PDFCreator\Utility\AttachmentUrlUpdater;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Extension\PDFCreator\Utility\ImageUrlUpdater;
use MediaWiki\Extension\PDFCreator\Utility\WikiFileResource;
use MediaWiki\Title\TitleFactory;
use MediaWiki\Utils\UrlUtils;
use RepoGroup;

class PDFHandlerProcessor extends ImageProcessor {

	/** @var TitleFactory */
	private $titleFactory;

	/** @var Config */
	private $config;

	/** @var RepoGroup */
	private $repoGroup;

	/** @var UrlUtils */
	private $urlUtils;

	/**
	 * @param TitleFactory $titleFactory
	 * @param Config $config
	 * @param RepoGroup $repoGroup
	 * @param UrlUtils $urlUtils
	 */
	public function __construct(
		TitleFactory $titleFactory, Config $config, RepoGroup $repoGroup, UrlUtils $urlUtils
	) {
		$this->titleFactory = $titleFactory;
		$this->config = $config;
		$this->repoGroup = $repoGroup;
		$this->urlUtils = $urlUtils;
	}

	/**
	 * @param array &$pages
	 * @param array &$images
	 * @param array &$attachments
	 * @param ExportContext|null $context
	 * @param string $module
	 * @param array $params
	 * @return void
	 */
	public function execute(
		array &$pages, array &$images, array &$attachments,
		?ExportContext $context = null, string $module = '', $params = []
	): void {
		// Find PDFHandler thumbs
		$imageFinder = new PDFHandlerThumbFinder( $this->config );
		$PdfImages = $imageFinder->execute( $pages, $images );

		/** @var WikiFileResource */
		foreach ( $PdfImages as $result ) {
			$filename = $result->getFilename();
			$images[$filename] = $result->getAbsolutePath();
		}

		// Update thumb url
		$imageUrlUpdater = new ImageUrlUpdater();
		$imageUrlUpdater->execute( $pages, $PdfImages );

		// Find corresponding attachments
		$attachmentFinder = new PDFHandlerAttachmentFinder( $this->titleFactory, $this->config, $this->repoGroup, $this->urlUtils );
		$PdfAttachments = $attachmentFinder->execute( $pages, $attachments );

		/** @var WikiFileResource */
		foreach ( $PdfAttachments as $result ) {
			$filename = $result->getFilename();
			$attachments[$filename] = $result->getAbsolutePath();
		}

		// Update attachment url and embedd attatchments
		$attachmentUrlUpdater = new AttachmentUrlUpdater();
		$attachmentUrlUpdater->execute( $pages, $PdfAttachments );

		// Update thumb width
		// In PDFHandlerThumbFinder the width is already handled.
	}

	/**
	 * @inheritDoc
	 */
	public function getPosition(): int {
		return 95;
	}
}
