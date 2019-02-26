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

$lllPath = 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:fe_users.';

$tempColumns = [
    'crdate' => [
        'exclude' => true,
        'config' => ['type' => 'passthrough'],
    ],
    'is_online' => [
        'exclude' => true,
        'config' => ['type' => 'passthrough'],
    ],
    'disable' => [
        'exclude' => true,
        'config' => ['type' => 'passthrough'],
    ],
    'date_of_birth' => [
        'exclude' => true,
        'config' => ['type' => 'passthrough'],
    ],
    'tx_t3forum_rank' => [
        'label' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_t3forum_domain_model_user_rank',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tx_t3forum_domain_model_user_rank',
            'foreign_class' => '\T3forum\T3forum\Domain\Model\User\Rank',
            'maxitems' => 1,
        ],
    ],
    'tx_t3forum_points' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_points',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_post_count' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_post_count',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_post_count_session' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_post_count_session',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_topic_count' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_topic_count',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_helpful_count' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_helpful_count',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_helpful_count_session' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_helpful_count_session',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_question_count' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_question_count',
        'config' => ['type' => 'none'],
    ],
    'tx_t3forum_topic_favsubscriptions' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_topic_favsubscriptions',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingleBox',
            'foreign_table' => 'tx_t3forum_domain_model_forum_topic',
            'MM' => 'tx_t3forum_domain_model_user_topicfavsubscription',
            'multiple' => true,
            'maxitems' => 9999,
            'minitems' => 0,
        ],
    ],
    'tx_t3forum_topic_subscriptions' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_topic_subscriptions',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingleBox',
            'foreign_table' => 'tx_t3forum_domain_model_forum_topic',
            'MM' => 'tx_t3forum_domain_model_user_topicsubscription',
            'multiple' => true,
            'maxitems' => 9999,
            'minitems' => 0,
        ],
    ],
    'tx_t3forum_forum_subscriptions' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_forum_subscriptions',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingleBox',
            'foreign_table' => 'tx_t3forum_domain_model_forum_forum',
            'MM' => 'tx_t3forum_domain_model_user_forumsubscription',
            'multiple' => true,
            'maxitems' => 9999,
            'minitems' => 0,
        ],
    ],
    'tx_t3forum_signature' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_signature',
        'config' => [
            'type' => 'text',
        ],
    ],
    'tx_t3forum_interests' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_interests',
        'config' => [
            'type' => 'text',
        ],
    ],
    'tx_t3forum_userfield_values' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_userfield_values',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_t3forum_domain_model_user_userfield_value',
            'foreign_field' => 'user',
            'maxitems' => 9999,
            'appearance' => [
                'collapse' => 0,
                'newRecordLinkPosition' => 'bottom',
            ],
        ],
    ],
    'tx_t3forum_read_forum' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_read_forum',
        'config' => [
            'type' => 'group',
            'foreign_table' => 'tx_t3forum_domain_model_forum_forum',
            'MM' => 'tx_t3forum_domain_model_user_readforum',
            'multiple' => true,
            'minitems' => 0,
        ],
    ],
    'tx_t3forum_read_topics' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_read_topics',
        'config' => [
            'type' => 'group',
            'foreign_table' => 'tx_t3forum_domain_model_forum_topic',
            'MM' => 'tx_t3forum_domain_model_user_readtopic',
            'multiple' => true,
            'minitems' => 0,
        ],
    ],
    'tx_t3forum_support_posts' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_support_posts',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingleBox',
            'foreign_table' => 'tx_t3forum_domain_model_forum_post',
            'MM' => 'tx_t3forum_domain_model_user_supportpost',
            'multiple' => true,
            'minitems' => 0,
        ],
    ],
    'tx_t3forum_use_gravatar' => [
        'label' => $lllPath . 'use_gravatar',
        'config' => [
            'type' => 'check',
        ],
    ],
    'tx_t3forum_contact' => [
        'label' => $lllPath . 'contact',
        'config' => [
            'type' => 'none',
        ],
    ],
    'tx_t3forum_facebook' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_facebook',
        'config' => [
            'type' => 'input',
            'size' => '255',
        ],
    ],
    'tx_t3forum_twitter' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_twitter',
        'config' => [
            'type' => 'input',
            'size' => '255',
        ],
    ],
    'tx_t3forum_google' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_google',
        'config' => [
            'type' => 'input',
            'size' => '255',
        ],
    ],
    'tx_t3forum_skype' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_skype',
        'config' => [
            'type' => 'input',
            'size' => '255',
        ],
    ],
    'tx_t3forum_job' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_job',
        'config' => [
            'type' => 'input',
            'size' => '255',
        ],
    ],
    'tx_t3forum_working_environment' => [
        'exclude' => true,
        'label' => $lllPath . 'tx_t3forum_working_environment',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['LLL:EXT:t3forum/Resources/Private/Language/locallang.xml:Working_Environment_0', 0],
                ['LLL:EXT:t3forum/Resources/Private/Language/locallang.xml:Working_Environment_1', 1],
                ['LLL:EXT:t3forum/Resources/Private/Language/locallang.xml:Working_Environment_2', 2],
                ['LLL:EXT:t3forum/Resources/Private/Language/locallang.xml:Working_Environment_3', 3],
                ['LLL:EXT:t3forum/Resources/Private/Language/locallang.xml:Working_Environment_4', 4],
            ],
            'default' => 0,
        ],
    ],
    'tx_t3forum_private_messages' => [
        'exclude' => true,
        'label' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_t3forum_domain_model_user_pm',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_t3forum_domain_model_user_privatemessage',
            'foreign_field' => 'feuser',
            'maxitems' => 9999,
            'appearance' => [
                'collapseAll' => 1,
                'newRecordLinkPosition' => 'bottom',
                'expandSingle' => 1,
            ],
        ],
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);

$GLOBALS['TCA']['fe_users']['types']['T3forum\T3forum\Domain\Model\User\FrontendUser'] =
    $GLOBALS['TCA']['fe_users']['types']['0'];

$GLOBALS['TCA']['fe_users']['types']['T3forum\T3forum\Domain\Model\User\FrontendUser']['showitem'] .=
    ',--div--;LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:fe_users.tx_t3forum.tab.settings,'
    . ' tx_t3forum_points, tx_t3forum_post_count, tx_t3forum_topic_count, tx_t3forum_helpful_count,'
    . ' tx_t3forum_question_count, tx_t3forum_rank,tx_t3forum_signature, tx_t3forum_userfield_values,'
    . ' tx_t3forum_use_gravatar, tx_t3forum_contact, tx_t3forum_working_environment, tx_t3forum_private_messages,'
    . ' tx_t3forum_post_count_session, tx_t3forum_helpful_count_session';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem('fe_users', 'tx_extbase_type', [
    $lllPath . 'tx_extbase_type.t3forum',
    'T3forum\T3forum\Domain\Model\User\FrontendUser',
]);
