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

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_privatemessage.';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_privatemessage',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/User/pm.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'message, feuser, opponent, type, user_read, crdate'
    ],
    'types' => [
        '1' => ['showitem' => 'message, feuser, opponent, type, user_read, crdate'],
    ],
    'columns' => [
        'message' => [
            'label' => $lllPath . 'message',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_user_privatemessage_text',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\PrivateMessageText',
                'maxitems' => 1,
                'appearance' => [
                    'collapseAll' => 1,
                    'newRecordLinkPosition' => 'bottom',
                    'expandSingle' => 1,
                ],
            ],
        ],
        'feuser' => [
            'label' => $lllPath . 'feuser',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUser',
                'maxitems' => 1
            ],
        ],
        'opponent' => [
            'label' => $lllPath . 'opponent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUser',
                'maxitems' => 1
            ],
        ],
        'type' => [
            'label' => $lllPath . 'type',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['sender', \Mittwald\Typo3Forum\Domain\Model\User\PrivateMessage::TYPE_SENDER],
                    ['recipient', \Mittwald\Typo3Forum\Domain\Model\User\PrivateMessage::TYPE_RECIPIENT],
                ],
                'default' => 0,
            ],
        ],
        'user_read' => [
            'label' => $lllPath . 'user_read',
            'config' => [
                'type' => 'check'
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tstamp',
            'config' => [
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date',
            ],
        ],
    ],
];
