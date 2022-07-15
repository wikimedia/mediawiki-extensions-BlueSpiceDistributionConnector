<?php

namespace BlueSpice\DistributionConnector\ContentImport;

use MediaWiki\Languages\LanguageFallback;

class ImportLanguage {

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
	 * @param array $availableLanguages
	 * @return string Fallback language, "en" if no fallbacks available (in case of unknown language, for example)
	 * @see LanguageFallback::getAll()
	 */
	public function getImportLanguage( $availableLanguages ): string {
		if ( in_array( $this->wikiLangCode, $availableLanguages ) ) {
			return $this->wikiLangCode;
		}
		$fallbackLanguages = $this->languageFallback->getAll( $this->wikiLangCode );
		foreach ( $fallbackLanguages as $fallbackLanguage ) {
			if ( in_array( $fallbackLanguage, $availableLanguages ) ) {
				return $fallbackLanguage;
			}
		}

		// Probably in case of some unknown language
		return 'en';
	}
}
