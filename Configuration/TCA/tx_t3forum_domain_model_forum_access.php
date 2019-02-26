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

$lllPath = 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_access.';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_typo3forum_domain_model_forum_access',
    'EXT:t3forum/Resources/Private/Language/locallang_csh_tx_typo3forum_domain_model_forum_access.xml'
);

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_access',
        'label' => 'operation',
        'type' => 'login_level',
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
        'iconfile' => 'EXT:t3forum/Resources/Public/Icons/Forum/Access.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'login_level,operation,negate,forum,affected_group'
    ],
    'types' => [
        '0' => ['showitem' => 'login_level,operation,negate,forum'],
        '1' => ['showitem' => 'login_level,operation,negate,forum'],
        '2' => ['showitem' => 'login_level,operation,negate,forum,affected_group'],
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
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_typo3forum_domain_model_forum_access',
                'foreign_table_where' => 'AND tx_typo3forum_domain_model_forum_access.uid=###REC_FIELD_l18n_parent### AND tx_typo3forum_domain_model_forum_access.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
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
        'operation' => [
            'exclude' => true,
            'label' => $lllPath . 'operation',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'items' => [
                    [$lllPath . 'operation.read', \Mittwald\Typo3Forum\Domain\Model\Forum\Access::TYPE_READ],
                    [$lllPath . 'operation.newTopic', \Mittwald\Typo3Forum\Domain\Model\Forum\Access::TYPE_NEW_TOPIC],
                    [$lllPath . 'operation.newPost', \Mittwald\Typo3Forum\Domain\Model\Forum\Access::TYPE_NEW_POST],
                    [$lllPath . 'operation.editPost', \Mittwald\Typo3Forum\Domain\Model\Forum\Access::TYPE_EDIT_POST],
                    [$lllPath . 'operation.deletePost', \Mittwald\Typo3Forum\Domain\Model\Forum\Access::TYPE_DELETE_POST],
                    [$lllPath . 'operation.moderation', \Mittwald\Typo3Forum\Domain\Model\Forum\Access::TYPE_MODERATE],
                ],
            ],
        ],
        'negate' => [
            'exclude' => true,
            'label' => $lllPath . 'negate',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'forum' => [
            'exclude' => true,
            'label' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_forum',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\Forum\Forum',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_forum',
                'maxitems' => 1
            ],
        ],
        'affected_group' => [
            'exclude' => true,
            'label' => $lllPath . 'group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_groups',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUserGroup',
                'maxitems' => 1
            ],
        ],
        'login_level' => [
            'exclude' => true,
            'label' => $lllPath . 'login_level',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$lllPath . 'login_level.everyone', 0],
                    [$lllPath . 'login_level.anylogin', 1],
                    [$lllPath . 'login_level.specific', 2],
                ],
            ],
        ],
    ],
];
