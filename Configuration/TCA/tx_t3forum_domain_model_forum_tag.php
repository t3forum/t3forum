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

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_tag.';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_tag',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Forum/Tag.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'name,tstamp,crdate,topic_count,feuser',
    ],
    'types' => [
        '1' => ['showitem' => 'name,tstamp,crdate,topic_count,feuser'],
    ],
    'columns' => [
        'name' => [
            'label' => $lllPath . 'name',
            'config' => [
                'type' => 'input',
            ]
        ],
        'tstamp' => [
            'label' => $lllPath . 'tstamp',
            'config' => [
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date',
            ]
        ],
        'crdate' => [
            'label' => $lllPath . 'crdate',
            'config' => [
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date',
            ]
        ],
        'topic_count' => [
            'label' => $lllPath . 'topicCount',
            'config' => [
                'type' => 'none',
            ],
        ],
        'feuser' => [
            'label' => $lllPath . 'feuser',
            'config' => [
                'type' => 'select',
                'size' => 10,
                'maxitems' => 99999,
                'foreign_table' => 'fe_users',
                'MM' => 'tx_typo3forum_domain_model_forum_tag_user',
                'renderType' => 'selectSingleBox',
            ],
        ],
    ]
];
