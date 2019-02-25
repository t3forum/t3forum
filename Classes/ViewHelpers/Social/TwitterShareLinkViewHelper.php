<?php
namespace T3forum\T3forum\ViewHelpers\Social;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TwitterShareLinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
    }

    /**
     * Render a share button
     *
     * @param string $title Title for share
     * @param string $text Title for share
     * @param string $shareUrl Title for share
     * @return string
     */
    public function render($title = null, $text = null, $shareUrl = null)
    {
        // check defaults
        if (empty($this->arguments['name'])) {
            $this->tag->addAttribute('name', 'fb_share');
        }

        if (empty($this->arguments['type'])) {
            $this->tag->addAttribute('type', 'link');
        }

        if (empty($this->arguments['target'])) {
            $this->tag->addAttribute('target', '_blank');
        }

        $url = 'https://twitter.com/intent/tweet';

        $url .= '?original_referer=' . urldecode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        $url .= '&url=';
        if ($shareUrl) {
            $url .= urldecode($shareUrl);
        } else {
            $url .= urldecode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        }

        if ($title) {
            $url .= '&p[title]=' . urldecode($title);
        }

        if ($text) {
            $url .= '&text=' . urldecode($text);
        }

        $this->tag->addAttribute('href', $url);
        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }
}
