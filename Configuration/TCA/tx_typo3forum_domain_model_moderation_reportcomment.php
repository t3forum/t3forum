<?php

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_reportcomment.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_reportcomment',
        'label' => 'text',
        'tstamp' => 'tstamp',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Moderation/ReportComment.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'report,author,text,tstamp'
    ],
    'types' => [
        '1' => ['showitem' => 'report,author,text'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.hidden',
            'config'  => [
                'type' => 'check',
            ],
        ],
        'tstamp' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.tstamp',
            'config'  => [
                'type' => 'passthrough',
            ],
        ],
        'report' => [
            'exclude' => true,
            'label'   => $lllPath . 'report',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_moderation_report',
                'maxitems' => 1,
            ],
        ],
        'author' => [
            'exclude' => true,
            'label'   => $lllPath . 'author',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1,
            ],
        ],
        'text' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_report.moderator',
            'config' => [
                'type' => 'text',
            ],
        ],
    ],
];
