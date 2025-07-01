<?php

$EM_CONF['ns_news_advancedsearch'] = [
    'title' => 'Advanced News Search for TYPO3',
    'description' => 'Extend the TYPO3 News extension with powerful advanced search functionality. Easily filter news by categories, authors, tags, and custom fields with precision and speed—thanks to optimized search algorithms designed for relevance.',
    
    'category' => 'plugin',
    'author' => 'Team T3Planet',
    'author_email' => 'info@t3planet.de',
    'author_company' => 'T3Planet',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'version' => '13.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-13.9.99',
            'news' => '11.0.0-12.0.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
