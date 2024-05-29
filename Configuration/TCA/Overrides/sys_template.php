<?php

defined('TYPO3') || die('Access denied.');

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'ns_news_advancedsearch',
    'Configuration/TypoScript',
    'News advanced search'
);
