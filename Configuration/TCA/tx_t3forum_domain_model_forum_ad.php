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

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_ad.';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_ad',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'delete' => 'deleted',
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Forum/Ad.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'name,alt_text,url,path,active,category,groups',
    ],
    'types' => [
        '1' => ['showitem' => 'name,alt_text,url,path,active,category,groups'],
    ],
    'columns' => [
        'name' => [
            'label' => $lllPath . 'name',
            'config' => [
                'type' => 'input',
            ],
        ],
        'alt_text' => [
            'label' => $lllPath . 'alt',
            'config' => [
                'type' => 'text',
            ],
        ],
        'url' => [
            'label' => $lllPath . 'url',
            'config' => [
                'type' => 'input',
            ],
        ],
        'path' => [
            'label' => $lllPath . 'path',
            'config' => [
                'type' => 'input',
            ],
        ],
        'active' => [
            'label' => $lllPath . 'active',
            'config' => [
                'type' => 'check',
            ],
        ],
        'category' => [
            'label' => $lllPath . 'category',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['all', 0],
                    ['forum only', 1],
                    ['topic only', 2],
                ],
                'default' => 0,
            ],
        ],
    ],
];