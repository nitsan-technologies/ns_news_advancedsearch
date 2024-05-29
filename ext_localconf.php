<?php

use NITSAN\NsNewsAdvancedsearch\Utility\ClassCacheManager;

if (!defined('TYPO3')) {
    die('Access denied.');
}
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFoundOnCHashError'] = 0;
// Add Custom fields to search Model
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['ext:news'] =
    ClassCacheManager::class . '->reBuild';

// Add Custom fields to search Model
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/Dto/Search'][] = 'ns_news_advancedsearch';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Controller/NewsController.php']['overrideSettings']
['ns_news_advancedsearch'] = 'NITSAN\\NsNewsAdvancedsearch\\Hooks\\NewsControllerSettings->modify';

// Modify Repository query for advanced filter
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Domain/Repository/AbstractDemandedRepository.php']['findDemanded']
['ns_news_advancedsearch'] = 'NITSAN\\NsNewsAdvancedsearch\\Hooks\\Repository->modify';
