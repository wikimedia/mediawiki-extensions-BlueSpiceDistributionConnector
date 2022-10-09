<?php

use BlueSpice\DistributionConnector\ContentFetch\ExportListParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BlueSpice\DistributionConnector\ContentFetch\ExportListParser
 */
class ExportListParserTest extends TestCase {

	/**
	 * @covers \BlueSpice\DistributionConnector\ContentFetch\ExportListParser::parse()
	 */
	public function testSuccess() {
		$exportListPath = __DIR__ . "/data/export_list.txt";

		$exportList = file_get_contents( $exportListPath );

		$parser = new ExportListParser();
		$data = $parser->parse( $exportList );

		$this->assertIsArray( $data, "Parsed data is not an array!" );
		$this->assertCount( 5, $data );

		$expectedData = [
			'Template:Hello_Template1' => [
				'lang' => 'en',
				'label' => 'HelloTemplate template1',
				'description' => 'Some description for "HelloTemplate" template.',
				'target_title' => 'Template:HelloTemplateTarget1'
			],
			'Template:Hello_Template2' => [
				'lang' => 'de',
				'label' => 'HelloTemplate template2',
				'description' => 'Some description for "HelloTemplate" template.',
				'target_title' => 'Template:HelloTemplateTarget2'
			],
			'Template:HelloTemplate3' => [
				'lang' => 'en',
				'label' => 'HelloTemplate template3',
				'description' => 'Template with no "target title".',
				'target_title' => 'Template:HelloTemplate3'
			],
			'Template:HelloTemplate4' => [
				'lang' => 'en',
				'label' => 'HelloTemplate template4',
				'description' => 'Check that whitespaces and empty lines does not break things',
				'target_title' => 'Template:HelloTemplate4'
			],
			'Template:HelloTemplate5' => [
				'lang' => 'en',
				'label' => 'HelloTemplate template5',
				'description' => 'Here we want to make sure that empty lines are trimmed.',
				'target_title' => 'Template:HelloTemplateTarget5'
			]
		];

		$this->assertEquals( $expectedData, $data );
	}

}
