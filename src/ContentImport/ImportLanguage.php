<?php

namespace BlueSpice\DistributionConnector\ContentImport;

use MediaWiki\Languages\LanguageFallback;

class ImportLanguage {

	/**
	 * Languages which can be used to import default BlueSpice pages.
	 * Currently "en" and "de" languages are the only languages, on which this content is available.
	 *
	 * @var string[]
	 */
	private $importLanguages = [
		'en',
		'de'
	];

	/**
	 * Language fallback service.
	 *
	 * @var LanguageFallback
	 */
	private $languageFallback;

	/**
	 * Current wiki language code.
	 *
	 * @var string
	 */
	private $wikiLangCode;

	/**
	 * @param LanguageFallback $languageFallback
	 * @param string $wikiLangCode
	 */
	public function __construct( LanguageFallback $languageFallback, string $wikiLangCode ) {
		$this->languageFallback = $languageFallback;
		$this->wikiLangCode = $wikiLangCode;
	}

	/**
	 * Returns fallback language (depending on language of wiki)
	 *
	 * @return string Fallback language, "en" if no fallbacks available (in case of unknown language, for example)
	 * @see LanguageFallback::getAll()
	 */
	public function getImportLanguage(): string {
		$fallbackLanguages = $this->languageFallback->getAll( $this->wikiLangCode );
		foreach ( $fallbackLanguages as $fallbackLanguage ) {
			if ( in_array( $fallbackLanguage, $this->importLanguages ) ) {
				return $fallbackLanguage;
			}
		}

		// Probably in case of some unknown language
		return 'en';
	}
}
