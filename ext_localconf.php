<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}
if (version_compare(TYPO3_branch, '8.0', '<')) {
	// For 7x Flexform Hook
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][] = \NITSAN\NsNewsAdvancedsearch\Hooks\FlexFormHook::class;
} else {
	// For 8x & 9x Flexform Hook
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
	   = \NITSAN\NsNewsAdvancedsearch\Hooks\FlexFormHook::class;
}

// Add Custom fields to search Model
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/Dto/Search'][] = 'ns_news_advancedsearch';

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Controller/NewsController.php']['overrideSettings']['ns_news_advancedsearch']
        = 'NITSAN\\NsNewsAdvancedsearch\\Hooks\\NewsControllerSettings->modify';

// Modify Repository query for advanced filter
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Domain/Repository/AbstractDemandedRepository.php']['findDemanded']['ns_news_advancedsearch'] 
		= 'NITSAN\\NsNewsAdvancedsearch\\Hooks\\Repository->modify';