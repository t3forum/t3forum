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

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_privatemessage_text.';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_privatemessage_text',
        'label' => 'uid',
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/User/pm.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'message_text',
    ],
    'types' => [
        '1' => ['showitem' => 'message_text']
    ],
    'columns' => [
        'message_text' => [
            'label' => $lllPath . 'message_text',
            'config' => [
                'type' => 'text',
            ],
        ],
    ]
];
