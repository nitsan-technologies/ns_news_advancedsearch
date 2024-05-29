<?php

$EM_CONF['ns_news_advancedsearch'] = [
    'title' => 'Advanced Search for EXT:news',
    'description' => 'Enhance your TYPO3 News Extension with the Advanced TYPO3 News Extension. Expand your news search capabilities with detailed, precise results and the ability to filter content by specific fields using the Advanced Extension. This extension incorporates specially crafted search algorithms to ensure both speed and relevance in your searches. 
    
    *** Live Demo: https://demo.t3planet.com/t3-extensions/news-advancedsearch *** Documentation & Free Support: https://t3planet.com/typo3-news-search-extension',
    'category' => 'plugin',
    'author' => 'T3: Nilesh Malankiya, T3: Maulik Lakhnotra, QA: Krishna Dhapa',
    'author_email' => 'sanjay@nitsan.in',
    'author_company' => 'T3Planet // NITSAN',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-11.5.37',
            'news' => '8.6.0-10.0.3',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
