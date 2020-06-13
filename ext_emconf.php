<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '[NITSAN] Advanced Search for EXT:news',
    'description' => 'Do you want more rich search features into your favourite EXT:news? By installing this extension, you can search by category, title, teaser etc. Know  more in manual. Live-Demo: https://demo.t3terminal.com/t3t-extensions/news-comments-1/news-advance-search/ You can download PRO version for more-features & free-support at https://t3terminal.com/advanced-search-for-typo3-news-extension/',
    'category' => 'plugin',
    'author' => 'T3: Keval Pandya, QA:Vandna Kalivada',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'NITSAN Technologies Pvt Ltd',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.0-10.4.99',
            'news' => '3.0.0-8.9.9',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
