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

$tempColumns = [
    'tx_t3forum_user_mod' => [
        'label' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:fe_users.user_mod',
        'config' => [
            'type' => 'check',
        ],
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_groups', $tempColumns, 1);

$GLOBALS['TCA']['fe_groups']['types']['T3forum\T3forum\Domain\Model\User\FrontendUserGroup'] =
    $GLOBALS['TCA']['fe_groups']['types']['0'];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'fe_groups',
    'tx_extbase_type',
    [
        'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:fe_groups.tx_extbase_type.t3forum',
        'T3forum\T3forum\Domain\Model\User\FrontendUserGroup',
    ]
);
$GLOBALS['TCA']['fe_groups']['types']['Mittwald\T3forum\Domain\Model\User\FrontendUserGroup']['showitem'] .=
    ',--div--;LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:fe_users.tx_t3forum.tab.settings,' .
    'tx_t3forum_user_mod';
