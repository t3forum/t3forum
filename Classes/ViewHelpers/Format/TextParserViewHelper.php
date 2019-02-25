<?php
namespace T3forum\T3forum\ViewHelpers\Format;

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

use T3forum\T3forum\Domain\Model\Forum\Post;
use T3forum\T3forum\Domain\Repository\Forum\PostRepository;
use T3forum\T3forum\TextParser\TextParserService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper that performs text parsing operations on text input.
 */
class TextParserViewHelper extends AbstractViewHelper
{
    /**
     * The text parser service
     *
     * @var TextParserService
     * @inject
     */
    protected $textParserService;

    /**
     * An instance of the post repository class. The repository is needed
     * only when a rendered post text has to be persisted in the database.
     *
     * @var PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * Renders the input text.
     *
     * @param string $configuration The configuration path
     * @param Post $post
     * @param string $content The content to be rendered. If NULL, the node content will be rendered instead.
     * @return string The rendered text
     */
    public function render(
        $configuration = 'plugin.tx_t3forum.settings.textParsing',
        Post $post = null,
        $content = null
    ) {
        $this->textParserService->setControllerContext($this->controllerContext);
        $this->textParserService->loadConfiguration($configuration);

        if ($post !== null) {
            $renderedText = $this->textParserService->parseText($post->getText());
        } else {
            $renderedText = $this->textParserService->parseText($content ? $content : trim($this->renderChildren()));
        }
        return $renderedText;
    }
}
