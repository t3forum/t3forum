<?php
namespace T3forum\T3forum\Configuration;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationBuilder implements SingletonInterface
{
    /**
     * @var TypoScriptService
     * @inject
     */
    protected $typoScriptService;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $persistenceSettings = [];

    /**
     * @return array
     */
    public function getSettings()
    {
        if (!count($this->settings)) {
            $this->loadTypoScript();
        }

        return $this->settings;
    }

    /**
     * @return array
     */
    public function getPersistenceSettings()
    {
        if (!count($this->persistenceSettings)) {
            $this->loadTypoScript();
        }

        return $this->persistenceSettings;
    }

    protected function loadTypoScript()
    {
        $typoScript = $this->getTypoScriptService()->convertTypoScriptArrayToPlainArray(
            $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_t3forum.']
        );
        $this->settings = $typoScript['settings'];
        $this->persistenceSettings = $typoScript['persistence'];
    }

    /**
     * This method is taken from the old implementation in AbstractRepository.
     * The reason this exists is that if somehow the inject doesn't work, we still have a working TypoScriptService
     *
     * @return TypoScriptService
     */
    protected function getTypoScriptService()
    {
        if (is_null($this->typoScriptService)) {
            $this->typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        }

        return $this->typoScriptService;
    }
}
