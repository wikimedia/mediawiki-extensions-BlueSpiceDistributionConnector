<?php

require_once( __DIR__."/includes/AutoLoader.php");
require_once( __DIR__."/BlueSpiceDistribution.hooks.php" );

$wgMessagesDirs['BlueSpiceDistribution'] = __DIR__ . '/i18n';

$aResourceModuleTemplate = array (
	'localBasePath' => 'extensions/BlueSpiceDistribution/BSDistConnector/resources/',
	'remoteExtPath' => 'BlueSpiceDistribution/BSDistConnector/resources'
);

$wgResourceModules['ext.bluespice.distribution'] = array (
	'scripts' => 'bluespice.distribution.js',
	'targets' => array ( 'mobile' ),
	'position' => 'bottom',
	) + $aResourceModuleTemplate;

$wgResourceModules['ext.bluespice.wikicategorytagcloud'] = array (
	'styles' => 'bluespice.wikicategorytagcloud.css'
	) + $aResourceModuleTemplate;
