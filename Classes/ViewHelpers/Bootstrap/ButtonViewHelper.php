<?php
namespace T3forum\T3forum\ViewHelpers\Bootstrap;

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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper;

/**
 * ViewHelper that renders a big button.
 */
class ButtonViewHelper extends ActionViewHelper
{
    /**
     *
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('primary', 'boolean', 'Primary button', false, false);
        $this->registerArgument('label', 'string', 'Button label', true);
        $this->registerArgument('icon', 'string', 'Icon', false, null);
    }

    /**
     *
     */
    public function initialize()
    {
        parent::initialize();
        $class = 'btn';
        if ($this->arguments['primary'] === true) {
            $class .= ' btn-primary';
        }
        $this->tag->addAttribute('class', $class);
    }

    /**
     *
     */
    public function renderChildren()
    {
        if ($this->arguments['icon']) {
            $content = '<i class="tx-t3forum-icon-16-' . $this->arguments['icon'] . '"></i> ';
        } else {
            $content = '';
        }
        $content .= LocalizationUtility::translate($this->arguments['label'], 't3forum');
        return $content;
    }
}
