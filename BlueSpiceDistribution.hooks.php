<?php

$wgHooks['BeforePageDisplay'][] = 'BlueSpiceDistributionHooks::onBeforePageDisplay';
$wgHooks['MinervaPreRender'][] = 'BlueSpiceDistributionHooks::onMinervaPreRender';
$wgHooks['ResourceLoaderRegisterModules'][] = 'BlueSpiceDistributionHooks::onResourceLoaderRegisterModules';
$wgHooks['UserLoginForm'][] = 'BlueSpiceDistributionHooks::onUserLoginForm';

$wgHooks['BSInsertMagicAjaxGetData'][]
	= 'BlueSpiceDistributionHooks::onBSInsertMagicAjaxGetDataCategoryTree';
$wgHooks['BSInsertMagicAjaxGetData'][]
	= 'BlueSpiceDistributionHooks::onBSInsertMagicAjaxGetDataCite';
$wgHooks['BSInsertMagicAjaxGetData'][]
	= 'BlueSpiceDistributionHooks::onBSInsertMagicAjaxGetDataQuiz';
$wgHooks['BSInsertMagicAjaxGetData'][]
	= 'BlueSpiceDistributionHooks::onBSInsertMagicAjaxGetDataEmbedVideo';
$wgHooks['BSInsertMagicAjaxGetData'][]
	= 'BlueSpiceDistributionHooks::onBSInsertMagicAjaxGetDataDynamicPageList';