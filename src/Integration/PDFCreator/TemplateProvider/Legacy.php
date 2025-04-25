<?php

namespace BlueSpice\DistributionConnector\Integration\PDFCreator\TemplateProvider;

use DOMDocument;
use DOMElement;
use MediaWiki\Config\ConfigFactory;
use MediaWiki\Context\RequestContext;
use MediaWiki\Extension\PDFCreator\ITemplateProvider;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Extension\PDFCreator\Utility\Template;
use MediaWiki\Extension\PDFCreator\Utility\TemplateResources;

class Legacy implements ITemplateProvider {

	/** @var string */
	private $templateDir = '';

	/** @var string */
	private $defaultTemplate = '';

	/**
	 * @param ConfigFactory $configFactory
	 */
	public function __construct( ConfigFactory $configFactory ) {
		$config = $configFactory->makeConfig( 'bsg' );
		$this->templateDir = $config->get( "PDFCreatorLegacyTemplateDirectory" );
		$this->defaultTemplate = $config->get( "PDFCreatorDefaultLegacyTemplate" );
	}

	/**
	 * @return array
	 */
	public function getTemplateNames(): array {
		$templateNames = [];

		if ( $this->templateDir === '' ) {
			return $templateNames;
		}

		foreach ( glob( "{$this->templateDir}/*", GLOB_ONLYDIR ) as $dir ) {
			$dirname = basename( $dir );
			if ( file_exists( "{$this->templateDir}/$dirname/template.php" ) ) {
				$templateNames[] = $dirname;
			}
		}

		return $templateNames;
	}

	/**
	 * @param ExportContext $context
	 * @param string $name
	 * @return Template|null
	 */
	public function getTemplate( ExportContext $context, string $name = '' ): ?Template {
		$templates = $this->getTemplateNames();
		$templateName = $this->defaultTemplate;
		if ( $name !== '' && in_array( $name, $templates ) ) {
			$templateName = $name;
		} elseif ( $this->defaultTemplate !== '' && in_array( $this->defaultTemplate, $templates ) ) {
			$templateName = $this->defaultTemplate;
		} elseif ( count( $templates ) > 0 ) {
			$templateName = $templates[0];
		} else {
			return null;
		}

		$data = include "{$this->templateDir}/{$templateName}/template.php";
		$html = file_get_contents( "{$this->templateDir}/{$templateName}/template.html" );

		$context = RequestContext::getMain();
		$lang = $context->getLanguage()->getCode();

		$html = $this->replaceMeta( $html );
		$html = $this->replaceContent( $html );
		$html = $this->replaceMessages( $html, $data['messages'], $lang );

		$dom = new DOMDocument();
		$dom->loadHtml( $html );

		$intro = '';
		$header = '';
		$headerEl = $dom->getElementById( 'bs-runningheaderfix' );
		if ( $headerEl instanceof DOMElement ) {
			$headerEl->setAttribute( 'class', 'bs-runningheaderfix' );
			$header = $dom->saveHtml( $headerEl );
		}
		$footer = '';
		$footerEl = $dom->getElementById( 'bs-runningfooterfix' );
		if ( $footerEl instanceof DOMElement ) {
			$footerEl->setAttribute( 'class', 'bs-runningfooterfix' );
			$footer = $dom->saveHtml( $footerEl );
		}
		$body = '';
		$bodyEl = $dom->getElementById( 'bs-content' );
		if ( $bodyEl instanceof DOMElement ) {
			foreach ( $bodyEl->childNodes as $childNode ) {
				$body .= $dom->saveHtml( $childNode );
			}
		}
		$outro = '';

		$template = new Template(
			$body, $header, $footer, $intro, $outro,
			$this->getResources( $data, $this->templateDir, $templateName ),
			[],
			[]
		);

		return $template;
	}

	/**
	 * @param array $data
	 * @param string $templateDir
	 * @param string $templateName
	 * @return TemplateResources
	 */
	private function getResources( array $data, string $templateDir, string $templateName ): TemplateResources {
		$stylesheets = [];
		$fonts = [];
		if ( isset( $data['resources']['STYLESHEET'] ) ) {
			foreach ( $data['resources']['STYLESHEET'] as $path ) {
				$name = substr( $path, strrpos( $path, '/' ) + 1 );
				if ( substr( $name, strrpos( $name, '.' ) + 1 ) === 'css' ) {
					$stylesheets[$name] = $this->makeAbsolutePath( $path, $templateDir, $templateName );
				} elseif ( substr( $name, strrpos( $name, '.' ) + 1 ) === 'ttf' ) {
					$fonts[$name] = $this->makeAbsolutePath( $path, $templateDir, $templateName );
				}
			}
		}

		$images = [];
		if ( isset( $data['resources']['IMAGE'] ) ) {
			foreach ( $data['resources']['IMAGE'] as $path ) {
				$name = substr( $path, strrpos( $path, '/' ) + 1 );
				$images[$name] = $this->makeAbsolutePath( $path, $templateDir, $templateName );
			}
		}

		return new TemplateResources(
			$fonts,
			$stylesheets,
			[],
			$images
		);
	}

	/**
	 * @param string $path
	 * @param string $templateDir
	 * @param string $templateName
	 * @return string
	 */
	private function makeAbsolutePath( string $path, string $templateDir, string $templateName ): string {
		if ( strpos( $path, '../common' ) === 0 ) {
			$path = $templateDir . substr( $path, 2 );
		} else {
			$path = "{$templateDir}/{$templateName}/" . $path;
		}
		return $path;
	}

	/**
	 * @param string $html
	 * @return string
	 */
	private function replaceMeta( string $html ): string {
		$html = preg_replace_callback(
			'#<bs:meta key="(.*?)" />#',
			static function ( $matches ) {
				$key = $matches[1];
				if ( $key === 'exportdate' ) {
					$key = 'export-date';
				}
				return '{{{' . $key . '}}}';
			},
			$html
		);

		return $html;
	}

	/**
	 * @param string $html
	 * @return string
	 */
	private function replaceContent( string $html ): string {
		$html = preg_replace_callback(
			'#<bs:content key="(.*?)" />#',
			static function ( $matches ) {
				$key = $matches[1];
				return '<div id="bs-content">{{{' . $key . '}}}</div>';
			},
			$html
		);

		return $html;
	}

	/**
	 * @param string $html
	 * @param array $messages
	 * @param string $lang
	 * @return string
	 */
	private function replaceMessages( string $html, array $messages, string $lang ): string {
		$html = preg_replace_callback(
			'#<bs:msg key="(.*?)" />#',
			static function ( $matches ) use ( $messages, $lang ) {
				$key = $matches[1];
				if ( isset( $messages[$lang][$key] ) ) {
					return $messages[$lang][$key];
				} elseif ( isset( $messages[$lang]['en'] ) ) {
					return $messages[$lang]['en'];
				} else {
					return '';
				}
			},
			$html
		);

		return $html;
	}

}
