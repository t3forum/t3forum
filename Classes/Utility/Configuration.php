<?php
namespace Mittwald\Typo3Forum\Utility;

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
