<?php

/*
 * TYPO3 Forum Extension (EXT:t3forum)
 * https://github.com/t3forum
 *
 * COPYRIGHT NOTICE
 *
 * This extension was originally developed by
 * Mittwald CM Service GmbH & Co KG (https://www.mittwald.de)
 *
 * This script is part of the TYPO3 project. The TYPO3 project is free
 * software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_typo3forum_domain_model_forum_forum',
    'EXT:typo3_forum/Resources/Private/Language/locallang_csh_tx_typo3forum_domain_model_forum_forum.xml'
);

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_forum.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_forum',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'sortby' => 'sorting',
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Forum/Forum.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'title,description,children,acls,criteria,last_topic,last_post,displayed_pid',
    ],
    'types' => [
        '1' => ['showitem' => 'title,description,children,acls,criteria'],
    ],
    'palettes' => [
        'language' => ['showitem' => 'sys_language_uid, l18n_parent'],
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
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'foreign_table' => 'sys_category',
                'foreign_table_where' => 'AND sys_category.uid=###REC_FIELD_l18n_parent### AND sys_category.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => ''
            ]
        ],
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => $lllPath . 'title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => $lllPath . 'description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'children' => [
            'label' => $lllPath . 'children',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_forum',
                'foreign_field' => 'forum',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'appearance' => [
                    'collapse' => 0,
                    'newRecordLinkPosition' => 'bottom',
                ],
            ]
        ],
        'topics' => [
            'label' => $lllPath . 'topics',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_topic',
                'foreign_field' => 'forum',
                'maxitems' => 999999,
                'appearance' => [
                    'collapse' => 0,
                    'newRecordLinkPosition' => 'bottom',
                ],
            ]
        ],
        'criteria' => [
            'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_criteria',
            'config' => [
                'type' => 'select',
                'size' => 10,
                'maxitems' => 99999,
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_criteria',
                'MM' => 'tx_typo3forum_domain_model_forum_criteria_forum'
            ],
        ],
        'topic_count' => [
            'label' => $lllPath . 'topic_count',
            'config' => [
                'type' => 'none'
            ]
        ],
        'post_count' => [
            'label' => $lllPath . 'post_count',
            'config' => [
                'type' => 'none'
            ]
        ],
        'acls' => [
            'label' => $lllPath . 'acls',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_access',
                'foreign_field' => 'forum',
                'maxitems' => 9999,
                'appearance' => [
                    'collapse' => 0,
                    'newRecordLinkPosition' => 'bottom',
                ],
            ]
        ],
        'last_topic' => [
            'label' => $lllPath . 'last_topic',
            'config' => [
                'type' => 'none',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_topic',
                'minitems' => 0,
                'maxitems' => 1
            ]
        ],
        'last_post' => [
            'label' => $lllPath . 'last_post',
            'config' => [
                'type' => 'none',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'minitems' => 0,
                'maxitems' => 1
            ]
        ],
        'forum' => [
            'label' => $lllPath . 'forum',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\Forum\Forum',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_forum',
                'maxitems' => 1
            ]
        ],
        'subscribers' => [
            'label' => $lllPath . 'subscribers',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'fe_users',
                'MM' => 'tx_typo3forum_domain_model_user_forumsubscription',
                'MM_opposite_field' => 'tx_typo3forum_forum_subscriptions',
                'maxitems' => 9999,
                'size' => 10
            ]
        ],
        'readers' => [
            'label' => $lllPath . 'readers',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'fe_users',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUser',
                'MM' => 'tx_typo3forum_domain_model_user_readforum',
                'MM_opposite_field' => 'tx_typo3forum_read_forum',
                'size' => 10
            ],
        ],
        'displayed_pid' => [
            'label' => $lllPath . 'displayed_pid',
            'config' => [
                'type' => 'none',
            ],
        ],
        'sorting' => [
            'label' => $lllPath . 'sorting',
            'config' => [
                'type' => 'none',
            ],
        ],
    ],
];
