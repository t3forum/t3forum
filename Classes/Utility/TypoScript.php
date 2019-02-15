<?php
namespace T3forum\T3forum\Utility;

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

use T3forum\T3forum\Domain\Exception\TextParser\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Utility module for TypoScript related functions.
 */
class TypoScript
{
    /**
     * The extbase configuration manager.
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager = null;

    /**
     * Loads the typoscript configuration from a certain setup path.
     *
     * @param string $configurationPath The typoscript path
     * @return array The typoscript configuration for the specified path.
     * @throws Exception
     */
    public function loadTyposcriptFromPath($configurationPath)
    {
        $setup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $pathSegments = GeneralUtility::trimExplode('.', $configurationPath);
        $lastSegment = array_pop($pathSegments);
        foreach ($pathSegments as $segment) {
            if (!array_key_exists($segment . '.', $setup)
            ) {
                throw new Exception(
                    'TypoScript object path "' . htmlspecialchars($configurationPath) . '" does not exist',
                    1253191023
                );
            }
            $setup = $setup[$segment . '.'];
        }
        return $setup[$lastSegment . '.'];
    }
}
