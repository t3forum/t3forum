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

$lllPath = 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_notification.';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_user_notification',
        'label' => 'uid',
        'label_alt' => 'feuser,crdate',
        'label_alt_force' => true,
        'type' => 'type',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'default_sortby' => 'ORDER BY crdate DESC',
        'hideTable' => true,
        'iconfile' => 'EXT:t3forum/Resources/Public/Icons/User/notification.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'feuser,post,tag,user_read,type,crdate',
    ],
    'types' => [
        '1' => ['showitem' => 'feuser,post,tag,user_read,type,crdate'],
        'Mittwald\Typo3Forum\Domain\Model\Forum\Post' => ['showitem' => 'feuser,post,user_read,type,crdate'],
        'Mittwald\Typo3Forum\Domain\Model\Forum\Tag' => ['showitem' => 'feuser,post,tag,user_read,type,crdate'],
    ],
    'columns' => [
        'crdate' => [
            'exclude' => true,
            'label' => $lllPath . 'crdate',
            'config' => [
                'type' => 'none',
                'format' => 'date',
                'eval' => 'date',
            ],
        ],
        'feuser' => [
            'label' => $lllPath . 'feuser',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\User\FrontendUser',
                'maxitems' => 1,
            ],
        ],
        'user_read' => [
            'label' => $lllPath . 'user_read',
            'config' => [
                'type' => 'check'
            ],
        ],
        'post' => [
            'exclude' => true,
            'label' => $lllPath . 'post',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\Forum\Post',
                'maxitems' => 1,
            ],
        ],
        'tag' => [
            'exclude' => true,
            'label' => $lllPath . 'tag',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_tag',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\Forum\Tag',
                'maxitems' => 1,
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => $lllPath . 'type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_post', 'Mittwald\Typo3Forum\Domain\Model\Forum\Post'],
                    [$lllPath . 'tag', 'Mittwald\Typo3Forum\Domain\Model\Forum\Tag'],
                ],
            ],
        ],
    ],
];
