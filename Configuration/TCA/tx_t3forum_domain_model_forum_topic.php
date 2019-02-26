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
    'tx_typo3forum_domain_model_forum_topic',
    'EXT:typo3_forum/Resources/Private/Language/locallang_csh_tx_typo3forum_domain_model_forum_topic.xml'
);

$lllPath = 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic.';
$modelNamespace = '\Mittwald\Typo3Forum\Domain\Model';

if (version_compare(TYPO3_branch, '8.5', '<')) {
    // for TYPO3 8.5.0 or higher
    $systemLLLPath = 'lang/Resources/Private/Language/';
} else {
    // for TYPO3 8.4.99 or lower
    $systemLLLPath = 'lang/';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_topic',
        'type' => 'type',
        'label' => 'subject',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'iconfile' => 'EXT:typo3_forum/Resources/Public/Icons/Forum/Topic.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'type,subject,posts,author,subscribers,last_post,forum,target,question,criteria_options,solution,fav_subscribers,tags'
    ],
    'types' => [
        '0' => ['showitem' => 'type,subject,posts,author,subscribers,last_post,forum,readers,question,solution,fav_subscribers,tags'],
        '1' => ['showitem' => 'type,subject,forum,last_post,target'],
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
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.php:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27
            ],
        ],
        'crdate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.crdate',
            'config' => [
                'type' => 'passthrough'
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $systemLLLPath . 'locallang_general.xml:LGL.hidden',
            'config' => [
                'type' => 'check'
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => $lllPath . 'type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'minitems' => 1,
                'default' => 0,
                'items' => [
                    [$lllPath . 'type.0', 0],
                    [$lllPath . 'type.1', 1],
                ],
            ],
        ],
        'subject' => [
            'exclude' => true,
            'label' => $lllPath . 'subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'posts' => [
            'label' => $lllPath . 'posts',
            'config' => [
                'type' => 'inline',
                'foreign_sortby' => 'uid',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'foreign_field' => 'topic',
                'maxitems' => 9999,
                'appearance' => [
                    'collapse' => 0,
                    'newRecordLinkPosition' => 'bottom',
                ],
            ],
        ],
        'post_count' => [
            'exclude' => true,
            'label' => $lllPath . 'post_count',
            'config' => [
                'type' => 'none'
            ],
        ],
        'author' => [
            'label' => $lllPath . 'author',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'foreign_class' => $modelNamespace . '\User\FrontendUser',
                'maxitems' => 1
            ],
        ],
        'last_post' => [
            'label' => $lllPath . 'last_post',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'last_post_crdate' => [
            'label' => $lllPath . 'last_post_crdate',
            'config' => [
                'type' => 'none'
            ],
        ],
        'is_solved' => [
            'label' => $lllPath . 'is_solved',
            'config' => [
                'type' => 'none'
            ],
        ],
        'solution' => [
            'label' => $lllPath . 'solution',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_class' => $modelNamespace . '\Forum\Post',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_post',
                'maxitems' => 1,
                'items' => [['', 0]],
            ]
        ],
        'forum' => [
            'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_forum',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_class' => $modelNamespace . '\Forum\Forum',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_forum',
                'maxitems' => 1
            ],
        ],
        'closed' => [
            'label' => $lllPath . 'closed',
            'config' => [
                'type' => 'check'
            ],
        ],
        'sticky' => [
            'label' => $lllPath . 'sticky',
            'config' => [
                'type' => 'check'
            ],
        ],
        'question' => [
            'label' => $lllPath . 'question',
            'config' => [
                'type' => 'check'
            ],
        ],
        'criteria_options' => [
            'label' => 'LLL:EXT:typo3_forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_criteria_options',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 10,
                'maxitems' => 99999,
                'foreign_table' => 'tx_typo3forum_domain_model_forum_criteria_options',
                'MM' => 'tx_typo3forum_domain_model_forum_criteria_topic_options'
            ],
        ],
        'tags' => [
            'label' => $lllPath . 'tags',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 10,
                'maxitems' => 99999,
                'foreign_table' => 'tx_typo3forum_domain_model_forum_tag',
                'MM' => 'tx_typo3forum_domain_model_forum_tag_topic'
            ],
        ],
        'subscribers' => [
            'label' => $lllPath . 'subscribers',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_users',
                'foreign_class' => $modelNamespace . '\User\FrontendUser',
                'MM' => 'tx_typo3forum_domain_model_user_topicsubscription',
                'MM_opposite_field' => 'tx_typo3forum_topic_subscriptions',
                'maxitems' => 9999,
                'size' => 10
            ],
        ],
        'fav_subscribers' => [
            'label' => $lllPath . 'fav_subscribers',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_users',
                'foreign_class' => $modelNamespace . '\User\FrontendUser',
                'MM' => 'tx_typo3forum_domain_model_user_topicfavsubscription',
                'MM_opposite_field' => 'tx_typo3forum_topic_favsubscriptions',
                'maxitems' => 9999,
                'size' => 10
            ],
        ],
        'target' => [
            'label' => $lllPath . 'target',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_topic',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'readers' => [
            'label' => $lllPath . 'readers',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'fe_users',
                'foreign_class' => $modelNamespace . '\User\FrontendUser',
                'MM' => 'tx_typo3forum_domain_model_user_readtopic',
                'MM_opposite_field' => 'tx_typo3forum_read_topics',
                'size' => 10
            ],
        ],
    ],
];
