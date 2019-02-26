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
    'tx_typo3forum_domain_model_user_userfield_userfield',
    'EXT:t3forum/Resources/Private/Language/locallang_csh_tx_typo3forum_domain_model_user_userfield_userfield.xml'
);

$lllPath = 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_userfield_userfield.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_userfield_userfield',
        'label' => 'name',
        'type' => 'type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:t3forum/Resources/Public/Icons/User/Userfield/Userfield.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'type,name,typoscript_path,map_to_user_object',
    ],
    'types' => [
        '0' => ['showitem' => 'type,name,map_to_user_object'],
        'Mittwald\Typo3Forum\Domain\Model\User\Userfield\TyposcriptUserfield' => ['showitem' => 'type,name,typoscript_path,map_to_user_object'],
        'Mittwald\Typo3Forum\Domain\Model\User\Userfield\TextUserfield' => ['showitem' => 'type,name,map_to_user_object'],
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
                'foreign_table' => 'tx_typo3forum_domain_model_user_userfield_userfield',
                'foreign_table_where' => 'AND tx_typo3forum_domain_model_user_userfield_userfield.uid=###REC_FIELD_l18n_parent### AND tx_typo3forum_domain_model_user_userfield_userfield.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
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
        'type' => [
            'label' => $lllPath . 'type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$lllPath . 'type.undefined', 0],
                    [$lllPath . 'type.typoscript', 'Mittwald\Typo3Forum\Domain\Model\User\Userfield\TyposcriptUserfield'],
                    [$lllPath . 'type.text', 'Mittwald\Typo3Forum\Domain\Model\User\Userfield\TextUserfield'],
                ],
            ],
        ],
        'name' => [
            'exclude' => true,
            'label' => $lllPath . 'name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'typoscript_path' => [
            'exclude' => true,
            'label' => $lllPath . 'typoscript_path',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'map_to_user_object' => [
            'exclude' => true,
            'label' => $lllPath . 'map_to_user_object',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'checkbox' => true,
            ],
        ],
    ],
];
