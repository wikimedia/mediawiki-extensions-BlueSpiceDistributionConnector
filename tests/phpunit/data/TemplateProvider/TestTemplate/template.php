<?php
/**
 * This is the main description file for the template. It contains all
 * information necessary to load and process the template.
 */

return [

	/* A brief description. This information may be used in the user interface */
	'info' => [
		'name'      => 'Test Template',
	],

	/**
	 * The following resources are used in the conversion from xhtml to PDF.
	 * You may reference them in your template files
	 */
	'resources' => [
		// Some extra attachments to be included in every eport file
		'ATTACHMENT' => [],
		'STYLESHEET' => [
			'../common/stylesheets/page.css',
			'../common/stylesheets/mediawiki.css',
			'stylesheets/styles.css',
			'../common/stylesheets/geshi-php.css',
			'../common/stylesheets/tables.css',
			'../common/stylesheets/fonts.css',
			'../common/fonts/DejaVuSans.ttf',
			'../common/fonts/DejaVuSans-Bold.ttf',
			'../common/fonts/DejaVuSans-Oblique.ttf',
			'../common/fonts/DejaVuSans-BoldOblique.ttf',
			'../common/fonts/DejaVuSansMono.ttf',
			'../common/fonts/DejaVuSansMono-Bold.ttf',
			'../common/fonts/DejaVuSansMono-Oblique.ttf',
			'../common/fonts/DejaVuSansMono-BoldOblique.ttf'
		],
		'IMAGE' => [
			'images/logo.png'
		]
	],

	/**
	 * Here you can define messages for internationalization of your template.
	 */
	'messages' => [
		'en' => [
			'desc'        => 'This is the default PDFTemplate of BlueSpice for single article export.',
			'exportdate'  => 'Export:',
			'page'        => 'Page ',
			'of'          => ' of ',
			'disclaimer'  => 'This document was created with BlueSpice'
		]
	]
];
