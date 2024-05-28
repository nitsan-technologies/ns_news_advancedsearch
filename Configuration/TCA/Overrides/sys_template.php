<?php

defined('TYPO3') || die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$extKey = 'ns_news_advancedsearch';

// Adding fields to the tt_content table definition in TCA
ExtensionManagementUtility::addStaticFile(
    $extKey,
    'Configuration/TypoScript',
    'News advanced search'
);
