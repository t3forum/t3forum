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

$lllPath = 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_criteria.';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_criteria',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'delete' => 'deleted',
        'iconfile' => 'EXT:t3forum/Resources/Public/Icons/Forum/Criteria.png',
    ],
    'interface' => [
        'showRecordFieldList' => 'name,options,default_option',
    ],
    'types' => [
        '1' => ['showitem' => 'name,default_option'],
    ],
    'columns' => [
        'name' => [
            'label' => 'LLL:EXT:t3forum/Resources/Private/Language/locallang_db.xml:tx_typo3forum_domain_model_forum_criteria.name',
            'config' => [
                'type' => 'text',
            ],
        ],
        'options' => [
            'label' => $lllPath . 'options',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_typo3forum_domain_model_forum_criteria_options',
                'foreign_field' => 'criteria',
                'maxitems' => 9999,
                'foreign_sortby' => 'sorting',
                'appearance' => [
                    'collapseAll' => 1,
                    'newRecordLinkPosition' => 'bottom',
                    'expandSingle' => 1,
                ],
            ],
        ],
        'default_option' => [
            'label' => $lllPath . 'default_option',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'maxitems' => 1,
                'foreign_table' => 'tx_typo3forum_domain_model_forum_criteria_options',
                'foreign_class' => '\Mittwald\Typo3Forum\Domain\Model\Forum\CriteriaOption',
            ],
        ],
    ],
];
