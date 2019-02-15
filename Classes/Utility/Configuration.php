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

/**
 * Utility module for various configurations
 */
class Configuration
{
    /**
     * Returns the max file upload size as configured in PHP (php.ini).
     * Setting "upload_max_filesize" and "post_max_size" are taken into account.
     * The lower value is returned.
     *
     * @access public
     * @return string Human-readable representation of value
     */
    public function getMaxFileUploadSize()
    {
        $checkSettings = ['upload_max_filesize', 'post_max_size'];
        $lowerValue = ini_get($checkSettings[0]);
        foreach ($checkSettings as $setting) {
            $value = ini_get($setting);
            if ($this->convertToBytes($value) < $this->convertToBytes($lowerValue)) {
                $lowerValue = $value;
            }
        }
        return $lowerValue;
    }

    /**
     * Converts a human-readable value, e.g. 10M, to a numeric bytes representation
     *
     * @access public
     * @param string Human-readable representation of value
     * @return int Numeric representation of value
     */
    public function convertToBytes($humanReadableValue)
    {
        switch (substr($humanReadableValue, -1)) {
            case 'M':
            case 'm':
                return intval($humanReadableValue) * 1048576;
            case 'K':
            case 'k':
                return intval($humanReadableValue) * 1024;
            case 'G':
            case 'g':
                return int($humanReadableValue) * 1073741824;
            default:
                return $humanReadableValue;
        }
    }
}
