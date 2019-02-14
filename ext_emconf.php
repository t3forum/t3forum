<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'typo3_forum',
    'description' => 'Forum extension',
    'category' => 'plugin',
    'author' => 'TYPO3 Forum Extension Team',
    'author_email' => '',
    'author_company' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => 'typo3temp/typo3_forum,typo3temp/typo3_forum/gravatar',
    'clearCacheOnLoad' => 0,
    'version' => '8.7.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'static_info_tables' => '',
            'php' => '7.0.0-7.2.99',
        ],
        'suggests' => [
            'sr_feuser_register' => '',
            'secure_downloads' => '3.0',
        ],
    ],
];
