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
    'tx_typo3forum_domain_model_forum_attachment',
    'EXT:t3forum/Resources/Private/Language/locallang_csh_tx_typo3forum_domain_model_forum_attachment.xml'
);

$lllPath = 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_attachment.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_attachment',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => ['disabled' => 'hidden'],
        'iconfile' => 'EXT:t3forum/Resources/Public/Icons/Forum/Attachment.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'filename,real_filename,mime_type,download_count'
    ],
    'types' => [
        '1' => ['showitem' => 'filename,real_filename,mime_type,download_count'],
    ],
    'columns' => [
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
        'post' => [
            'exclude' => true,
            'label' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_post.topic',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_class' => '\T3forum\T3forum\Domain\Model\Forum\Post',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'maxitems' => 1
            ],
        ],
        'filename' => [
            'exclude' => true,
            'label' => $lllPath . 'filename',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'real_filename' => [
            'exclude' => true,
            'label' => $lllPath . 'real_filename',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'uploadfolder' => 'uploads/tx_typo3forum/attachments/',
                'minitems' => 1,
                'maxitems' => 1,
                'allowed' => '*',
                'disallowed' => ''
            ],
        ],
        'mime_type' => [
            'exclude' => true,
            'label' => $lllPath . 'mime_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'download_count' => [
            'exclude' => true,
            'label' => $lllPath . 'download_count',
            'config' => [
                'type' => 'none'
            ],
        ],
    ],
];
