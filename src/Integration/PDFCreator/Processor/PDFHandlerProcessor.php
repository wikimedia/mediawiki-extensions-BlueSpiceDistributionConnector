<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\Processor;

use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerAttachmentFinder;
use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerImageFinder;
use BlueSpice\DistributionConnector\Integration\PDFCreator\Utility\PDFHandlerThumbFinder;
use MediaWiki\Config\Config;
use MediaWiki\Extension\PDFCreator\Processor\ImageProcessor;
use MediaWiki\Extension\PDFCreator\Utility\BoolValueGet;
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
		// Find PDFHandler images
		$imageFinder = new PDFHandlerImageFinder( $this->config, $this->titleFactory, $this->urlUtils, $this->repoGroup );
		$pdfImages = $imageFinder->execute( $pages, $images );

		/** @var WikiFileResource */
		foreach ( $pdfImages as $result ) {
			$filename = $result->getFilename();
			$images[$filename] = $result->getAbsolutePath();
		}

		// Find PDFHandler thumbs
		$thumbFinder = new PDFHandlerThumbFinder( $this->config, $this->titleFactory, $this->urlUtils, $this->repoGroup );
		$pdfThumbs = $thumbFinder->execute( $pages, $images );

		/** @var WikiFileResource */
		foreach ( $pdfThumbs as $result ) {
			$filename = $result->getFilename();
			$images[$filename] = $result->getAbsolutePath();
		}

		// Update image url
		$imageUrlUpdater = new ImageUrlUpdater();
		$imageUrlUpdater->execute( $pages, $pdfImages );
		$imageUrlUpdater->execute( $pages, $pdfThumbs );

		if ( !isset( $params['attachments'] ) || !BoolValueGet::from( $params['attachments'] ) ) {
			return;
		}

		// Find corresponding attachments and update attachment url and embedd attatchments
		$attachmentFinder = new PDFHandlerAttachmentFinder(
			$this->titleFactory, $this->config, $this->repoGroup
		);
		$PdfAttachments = $attachmentFinder->execute( $pages, $images );

		/** @var WikiFileResource */
		foreach ( $PdfAttachments as $result ) {
			$filename = $result->getFilename();
			$attachments[$filename] = $result->getAbsolutePath();
		}

		// Update thumb width
		// In PDFHandlerThumbFinder the width is already handled.
	}

	/**
	 * @inheritDoc
	 */
	public function getPosition(): int {
		return 85;
	}
}
