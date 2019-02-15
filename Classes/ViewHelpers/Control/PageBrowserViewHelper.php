<?php
namespace T3forum\T3forum\ViewHelpers\Control;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper that renders a page browser.
 */
class PageBrowserViewHelper extends AbstractViewHelper
{
    /**
     * Renders the page browser.
     *
     * @param int $elements Number of elements
     * @param int $itemsPerPage Number of items per page
     * @param int $currentPage Current page
     * @return string HTML content of the page browser.
     */
    public function render($elements, $itemsPerPage, $currentPage = 1)
    {
        $output    = '';
        $pageCount = ceil($elements / $itemsPerPage);

        $output .= $this->renderChildItemWithPage(1, '«');
        $output .= $this->renderChildItemWithPage(max($currentPage - 1, 1), '‹');

        for ($page = 1; $page <= $pageCount; $page++) {
            $output .= $this->renderChildItemWithPage($page, $page);
        }

        $output .= $this->renderChildItemWithPage(min($currentPage + 1, $pageCount), '›');
        $output .= $this->renderChildItemWithPage($pageCount, '»');

        return $output;
    }

    /**
     * Renders a single page link.
     *
     * @param int $pageNum   The page number
     * @param int $pageLabel Page label
     * @return string Rendered page link
     */
    private function renderChildItemWithPage($pageNum, $pageLabel)
    {
        $this->templateVariableContainer->add('pageLabel', $pageLabel);
        $this->templateVariableContainer->add('page', $pageNum);
        $output = $this->renderChildren();
        $this->templateVariableContainer->remove('pageLabel');
        $this->templateVariableContainer->remove('page');
        return $output;
    }
}
