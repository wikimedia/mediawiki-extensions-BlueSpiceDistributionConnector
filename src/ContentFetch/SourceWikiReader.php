<?php

namespace BlueSpice\DistributionConnector\ContentFetch;

use MediaWiki\Json\FormatJson;
use MediaWiki\Status\Status;

class SourceWikiReader {

	/**
	 * API endpoint for wiki, which we want to work with.
	 * Example: "https://some_wiki/api.php"
	 *
	 * @var string
	 */
	private $endPoint;

	/**
	 * @param string $endPoint API endpoint for wiki, which we want to work with
	 * 		Example: "https://some_wiki/api.php"
	 */
	public function __construct( string $endPoint ) {
		$this->endPoint = $endPoint;
	}

	/**
	 * Get login token from a source wiki.
	 * This token can be used to log into source wiki using API.
	 *
	 * @return string
	 * @see SourceWikiReader::login()
	 */
	public function getLoginToken(): string {
		$params = [
			'action' => 'query',
			'meta' => 'tokens',
			'type' => 'login',
			'format' => 'json'
		];

		$url = $this->endPoint . '?' . http_build_query( $params );

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt' );

		$output = curl_exec( $ch );
		curl_close( $ch );

		$result = FormatJson::decode( $output, true );
		return $result['query']['tokens']['logintoken'];
	}

	/**
	 * Logs in, saves corresponding cookies.
	 * After that we are able to read out information from the source wiki.
	 *
	 * @param string $loginToken Login token, which is necessary to be obtained before logging in using API
	 * @param string $login User (or bot) login
	 * @param string $password User (or bot) password
	 * @return Status Good status if logged in successfully, fatal status with error otherwise
	 * @see SourceWikiReader::getLoginToken()
	 */
	public function login( string $loginToken, string $login, string $password ): Status {
		$params = [
			'action' => 'login',
			'lgname' => $login,
			'lgpassword' => $password,
			'lgtoken' => $loginToken,
			'format' => 'json'
		];

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $this->endPoint );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt' );

		$output = curl_exec( $ch );
		curl_close( $ch );

		$result = FormatJson::decode( $output, true );
		if ( isset( $result['error'] ) ) {
			return Status::newFatal( $result['error'] );
		}

		return Status::newGood();
	}

	/**
	 * Reads content of specified pages from the source wiki.
	 *
	 * @param array $pages List of pages titles, which contents we need
	 * @return Status If pages were retrieved successfully - good status is returned.
	 * 		Good status always contains array of retrieved pages as value. Array has such format:
	 * 		[
	 * 			page_title1 => page_content1,
	 * 			page_title2 => page_content2,
	 * 			...
	 * 		]
	 * 		If pages were not retrieved - fatal status with error message is returned.
	 * 		If some pages are missing - warning message for each page is added.
	 */
	public function readPagesContent( array $pages ): Status {
		$pages = implode( '|', $pages );

		$params = [
			"action" => "query",
			"prop" => "revisions",
			"titles" => $pages,
			"rvprop" => "content",
			"rvslots" => "main",
			"formatversion" => "2",
			"format" => "json"
		];

		$url = $this->endPoint . "?" . http_build_query( $params );

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt' );
		$output = curl_exec( $ch );
		curl_close( $ch );

		$result = FormatJson::decode( $output, true );
		if ( isset( $result['error'] ) ) {
			return Status::newFatal( $result['error'] );
		}

		$status = Status::newGood();

		$pages = [];
		foreach ( $result['query']['pages'] as $page ) {
			if ( isset( $page['missing'] ) ) {
				$status->warning( "Page \"{$page['title']}\" content was not found in the source wiki!" );
				continue;
			}
			$normalizedName = str_replace( ' ', '_', $page['title'] );
			$pages[$normalizedName] = $page['revisions'][0]['slots']['main']['content'];
		}

		$status->setResult( true, $pages );

		return $status;
	}
}
