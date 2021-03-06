<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_typo3forum_domain_model_forum_post',
    'EXT:typo3_forum/Resources/Private/Language/locallang_csh_tx_typo3forum_domain_model_forum_post.xml'
);

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_post.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_post',
        'label' => 'text',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Forum/Post.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'text,author,topic,attachments, helpful_count, supporters'
    ],
    'types' => [
        '1' => ['showitem' => 'text,author,topic,attachments, helpful_count, supporters'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.allLanguages', -1],
                    ['LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.default_value', 0],
                ],
            ],
            'default' => 0,
            'fieldWizard' => [
                'selectIcons' => [
                    'disabled' => false,
                ],
            ],
        ],
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check'
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.crdate',
            'config' => [
                'type' => 'passthrough'
            ],
        ],
        'text' => [
            'label' => $lllPath . 'text',
            'config' => [
                'type' => 'text'
            ],
        ],
        'rendered_text' => [
            'exclude' => true,
            'label' => $lllPath . 'rendered_text',
            'config' => [
                'type' => 'text'
            ],
        ],
        'author' => [
            'exclude' => true,
            'label' => $lllPath . 'author',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUser',
                'maxitems' => 1
            ],
        ],
        'author_name' => [
            'exclude' => true,
            'label' => $lllPath . 'author_name',
            'config' => [
                'type' => 'text'
            ],
        ],
        'topic' => [
            'exclude' => true,
            'label' => $lllPath . 'topic',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\Forum\Topic',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_topic',
                'maxitems' => 1
            ],
        ],
        'attachments' => [
            'exclude' => true,
            'label' => $lllPath . 'attachments',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_attachment',
                'foreign_field' => 'post',
                'maxitems' => 10
            ],
        ],
        'helpful_count' => [
            'label' => $lllPath . 'helpful_count',
            'config' => [
                'type' => 'none'
            ],
        ],
        'supporters' => [
            'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.supporters',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'fe_users',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUser',
                'MM' => 'tx_typo3forum_domain_model_user_supportpost',
                'MM_opposite_field' => 'tx_typo3forum_support_posts',
                'maxitems' => PHP_INT_MAX
            ],
        ],
    ],
];
