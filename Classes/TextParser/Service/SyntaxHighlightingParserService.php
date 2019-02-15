<?php
namespace T3forum\T3forum\TextParser\Service;

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

use T3forum\T3forum\TextParser\Service\AbstractGeshiService;

/**
 * Text parser class for parsing syntax highlighting.
 */
class SyntaxHighlightingParserService extends AbstractTextParserService
{
    /**
     * @var AbstractGeshiService
     * @inject
     */
    protected $xtGeshi;

    /**
     * Renders the parsed text.
     *
     * @param string $text The text to be parsed.
     * @return string The parsed text.
     */
    public function getParsedText($text)
    {
        return preg_replace_callback(
            ',\[code language=([a-z0-9]+)\](.*?)\[\/code\],is',
            [$this, 'parseSourceCode'],
            $text
        );
    }

    /**
     * Callback function that renders each source code block.
     *
     * @param array $matches PCRE matches.
     * @return string The rendered source code block.
     */
    protected function parseSourceCode($matches)
    {
        return $this->xtGeshi->getFormattedText(trim($matches[2]), trim($matches[1]));
    }
}
