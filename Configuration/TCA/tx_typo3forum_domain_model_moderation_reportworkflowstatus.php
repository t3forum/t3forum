<?php

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_reportworkflowstatus.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_reportworkflowstatus',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Moderation/ReportWorkflowStatus.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'name,icon,followup_status,initial,final'
    ],
    'types' => [
        '1' => ['showitem' => 'name,icon,followup_status,initial,final'],
    ],
    'columns' => [
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
        'name' => [
            'label' => $lllPath . 'name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'followup_status' => [
            'exclude' => true,
            'label' => $lllPath . 'followup_status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_typo3forum_domain_model_moderation_reportworkflowstatus',
                'MM' => 'tx_typo3forum_domain_model_moderation_reportworkflowstatus_mm',
                'maxitems' => 9999,
                'size' => 5
            ],
        ],
        'initial' => [
            'label' => $lllPath . 'initial',
            'config' => [
                'type' => 'check'
            ],
        ],
        'final' => [
            'label' => $lllPath . 'final',
            'config' => [
                'type' => 'check'
            ],
        ],
        'icon' => [
            'label' => $lllPath . 'icon',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'uploadfolder' => 'uploads/tx_typo3forum/workflowstatus/',
                'minitems' => 1,
                'maxitems' => 1,
                'allowed' => '*',
                'disallowed' => ''
            ],
        ],
    ],
];
