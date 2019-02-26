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

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_report.';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // die('Die Extension benötigt TYPO3 8.5.0 oder höher.');
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_moderation_report',
        'label' => 'post',
        'type' => 'type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Moderation/Report.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'type,reporter,moderator,workflow_status,comments, post, feuser',
    ],
    'types' => [
        '1' => ['showitem' => 'type,reporter,moderator,workflow_status,comments'],
        'Mittwald\Typo3Forum\Domain\Model\Moderation\UserReport' => ['showitem' => 'type,reporter,moderator,workflow_status,comments, feuser'],
        'Mittwald\Typo3Forum\Domain\Model\Moderation\PostReport' => ['showitem' => 'type,reporter,moderator,workflow_status,comments, post'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.crdate',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => $lllPath . 'type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$lllPath . 'type.userReport', 'Mittwald\Typo3Forum\Domain\Model\Moderation\UserReport'],
                    [$lllPath . 'type.postReport', 'Mittwald\Typo3Forum\Domain\Model\Moderation\PostReport'],
                ],
            ],
        ],
        'post' => [
            'label' => $lllPath . 'post',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'maxitems' => 1
            ],
        ],
        'feuser' => [
            'exclude' => true,
            'label' => $lllPath . 'user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1
            ],
        ],
        'reporter' => [
            'exclude' => true,
            'label' => $lllPath . 'reporter',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1
            ],
        ],
        'moderator' => [
            'exclude' => true,
            'label' => $lllPath . 'moderator',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'maxitems' => 1
            ],
        ],
        'workflow_status' => [
            'exclude' => true,
            'label' => $lllPath . 'workflow_status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_moderation_reportworkflowstatus',
                'maxitems' => 1
            ],
        ],
        'comments' => [
            'exclude' => true,
            'label' => $lllPath . 'comments',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_moderation_reportcomment',
                'foreign_field' => 'report',
                'maxitems' => 9999,
                'foreign_sortby' => 'tstamp',
                'appearance' => [
                    'collapseAll' => true,
                    'levelLinksPosition' => 'top',
                ],
            ],
        ],
    ],
];