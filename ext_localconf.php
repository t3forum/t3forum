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
 * http://www.gnu.org/copyleft/gpl.html.                               *
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

defined('TYPO3_MODE') or die();

$_EXTKEY = 't3forum';

/**
 * PageTSConfig
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TSconfig/pageTS.txt">'
);

/*
 * Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3forum.T3forum',
    'Pi1',
    [
        'Forum' => 'index, show, markRead, showUnread',
        'Topic' => 'show, new, create, solution, listLatest',
        'Post' => 'show, new, create, edit, update, delete, downloadAttachment',
        'User' => 'showMyProfile, index, list, subscribe, favSubscribe, show, disableUser, unDisableUser,
            listNotifications, listMessages, createMessage, newMessage, listPosts',
        'Report' => 'newUserReport, newPostReport, createUserReport, createPostReport',
        'Moderation' => 'indexReport, editReport, newReportComment, editTopic, updateTopic, updateUserReportStatus,
            updatePostReportStatus, createUserReportComment, createPostReportComment, topicConformDelete',
        'Tag' => 'list, show, new, create, listUserTags, newUserTag, deleteUserTag',
    ],
    [
        'Forum' => 'show, index, create, update, delete, markRead, showUnread',
        'Topic' => 'create',
        'Post' => 'new, create, edit, update, delete, downloadAttachment',
        'User' => 'showMyProfile, dashboard, subscribe, favSubscribe, listFavorites, listNotifications, listTopics,
            listMessages, createMessage,listPosts',
        'Report' => 'newUserReport, newPostReport, createUserReport, createPostReport',
        'Moderation' => 'indexReport, updateTopic, updateUserReportStatus, updatePostReportStatus, newReportComment,
            createUserReportComment, createPostReportComment',
        'Tag' => 'list, show, new, create, listUserTags, newUserTag, deleteUserTag',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3forum.T3forum',
    'Widget',
    [
        'User' => 'list',
        'Stats' => 'list',
    ],
    [
        'User' => 'list',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3forum.T3forum',
    'Ajax',
    [
        'Forum' => 'index',
        'Post' => 'preview, addSupporter, removeSupporter',
        'Tag' => 'autoComplete',
        'Ajax' => 'main, postSummary, loginbox'
    ],
    [
        'Forum' => 'index',
        'Post' => 'preview, addSupporter, removeSupporter',
        'Ajax' => 'main, postSummary, loginbox',
    ]
);

/*
 * TCE-Main hook for clearing all typo3_forum caches
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
    T3forum\T3forum\Cache\CacheManager::class . '->clearAll';

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['t3forum_main'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['t3forum_main'] = [];
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$_EXTKEY] =
    T3forum\T3forum\Ajax\Dispatcher::class . '::processRequest';

/*
 * Connect signals to slots.
 */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
);

$signalSlotDispatcher->connect(
    T3forum\T3forum\Domain\Model\Forum\Post::class,
    'postCreated',
    'T3forum\T3forum\Service\Notification\SubscriptionListener',
    'onPostCreated'
);
$signalSlotDispatcher->connect(
    T3forum\T3forum\Domain\Model\Forum\Topic::class,
    'topicCreated',
    T3forum\T3forum\Service\Notification\SubscriptionListener::class,
    'onTopicCreated'
);
$signalSlotDispatcher->connect(
    TYPO3\CMS\Extensionmanager\Service\ExtensionManagementService::class,
    'hasInstalledExtensions',
    T3forum\T3forum\Service\InstallService::class,
    'checkForMigrationOption'
);
$signalSlotDispatcher->connect(
    TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::class,
    'afterMappingSingleRow',
    T3forum\T3forum\Service\SettingsHydrator::class,
    'hydrateSettings'
);

/**
 * Scheduler tasks
 */
$locallang = 'Resources/Private/Language/locallang.xml';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][T3forum\T3forum\Scheduler\Counter::class] = [
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_t3forum_scheduler_counter_title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_t3forum_scheduler_counter_description',
    'additionalFields' => T3forum\T3forum\Scheduler\CounterAdditionalFieldProvider::class
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][T3forum\T3forum\Scheduler\DatabaseMigrator::class] = [
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_databaseMigrator_title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_databaseMigrator_description',
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][T3forum\T3forum\Scheduler\ForumRead::class] = [
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_forumRead_title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_forumRead_description',
    'additionalFields' => T3forum\T3forum\Scheduler\ForumReadAdditionalFieldProvider::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][T3forum\T3forum\Scheduler\Notification::class] = [
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_notification_title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_notification_description',
    'additionalFields' => T3forum\T3forum\Scheduler\NotificationAdditionalFieldProvider::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][T3forum\T3forum\Scheduler\SessionResetter::class] = [
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_sessionResetter_title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_sessionResetter_description',
    'additionalFields' => T3forum\T3forum\Scheduler\SessionResetterAdditionalFieldProvider::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][T3forum\T3forum\Scheduler\StatsSummary::class] = [
    'extension' => $_EXTKEY,
    'title' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_statsSummary_title',
    'description' => 'LLL:EXT:' . $_EXTKEY . '/' . $locallang . ':tx_typo3forum_scheduler_statsSummary_description',
    'additionalFields' => T3forum\T3forum\Scheduler\StatsSummaryAdditionalFieldProvider::class,
];
