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

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Forum Extension',
    'description' => 'Feature-rich forum extension for TYPO3 v8, based on EXT:typo3_forum (originally developed by Mittwald CM Service GmbH Co KG)',
    'category' => 'plugin',
    'author' => 'TYPO3 Forum Extension Team',
    'author_email' => '',
    'author_company' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => 'typo3temp/t3forum,typo3temp/t3forum/gravatar',
    'clearCacheOnLoad' => 0,
    'version' => '8.7.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
            'static_info_tables' => '',
            'php' => '7.0.0-7.2.99',
        ],
        'suggests' => [
            'sr_feuser_register' => '',
            'secure_downloads' => '3.0',
        ],
    ],
];
