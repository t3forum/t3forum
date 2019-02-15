<?php
namespace T3forum\T3forum\TextParser\Panel;

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

use Mittwald\Typo3Forum\TextParser\Panel\AbstractPanel;
use Mittwald\Typo3Forum\Domain\Repository\Format\SyntaxHighlightingRepository;
use Mittwald\Typo3Forum\Domain\Model\Format\SyntaxHighlighting;

/**
 *
 */
class SyntaxHighlightingPanel extends AbstractPanel
{
    /**
     * @TODO: what to do?
     *
     * @var SyntaxHighlightingRepository
     * @inject
     */
    protected $syntaxHighlightingRepository = null;

    /**
     * @TODO: what to do?
     *
     * @var array<SyntaxHighlighting>
     */
    protected $syntaxHighlightings = null;

    public function initializeObject()
    {
        $this->syntaxHighlightings = $this->syntaxHighlightingRepository->findAll();
    }

    /**
     * @TODO: what to do?
     *
     * @return array<array>
     */
    public function getItems()
    {
        $result = [];

        foreach ($this->syntaxHighlightings as $syntaxHighlighting) {
            $result[] = $syntaxHighlighting->exportForMarkItUp();
        }
        return [[
            'name'      => $this->settings['title'],
            'className' => $this->settings['iconClassName'],
            'openWith'  => '[code]',
            'closeWith' => '[/code]',
            'dropMenu'  => $result
        ]];
    }
}
