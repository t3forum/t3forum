<?php
namespace T3forum\T3forum\TextParser;

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
use T3forum\T3forum\Service\AbstractService;
use T3forum\T3forum\TextParser\Service\AbstractTextParserService;
use T3forum\T3forum\Utility\TypoScript;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * Service class for parsing text values for display. This service handles
 * for example the rendering of bb codes, smileys, etc.
 */
class TextParserService extends AbstractService
{
    /**
     * An instance of the Extbase object manager.
     *
     * @var ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * An instance of the typo3_forum typoscript reader. Is used to read the
     * text parser's typoscript configuration.
     *
     * @var TypoScript
     * @inject
     */
    protected $typoscriptReader;

    /**
     * An array of the parsing services that are to be used to render text input.
     *
     * @var array<AbstractTextParserService>
     */
    protected $parsingServices;

    /**
     * The viewHelper variable container. This needs to be set when this service is
     * called from a viewHelper context.
     *
     * @var ViewHelperVariableContainer
     * @inject
     */
    protected $viewHelperVariableContainer;

    /**
     * The current controller context.
     *
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * Sets the current Extbase controller context.
     *
     * @param ControllerContext $controllerContext
     */
    public function setControllerContext(ControllerContext $controllerContext)
    {
        $this->controllerContext = $controllerContext;
    }

    /**
     * Loads the text parser configuration from a certain configuration path.
     *
     * @param string $configurationPath The typoscript configuration path.
     * @return void
     * @throws Exception
     */
    public function loadConfiguration($configurationPath = 'plugin.tx_t3forum.settings.textParsing')
    {
        if ($this->settings !== null) {
            return;
        }

        $this->settings = $this->typoscriptReader->loadTyposcriptFromPath($configurationPath);
        foreach ($this->settings['enabledServices.'] as $key => $className) {
            if (substr($key, -1, 1) === '.') {
                continue;
            }

            /** @var $newService \T3forum\T3forum\TextParser\Service\AbstractTextParserService */
            $newService = $this->objectManager->get($className);
            if ($newService instanceof \T3forum\T3forum\TextParser\Service\AbstractTextParserService) {
                $newService->setSettings((array)$this->settings['enabledServices.'][$key . '.']);
                $newService->setControllerContext($this->controllerContext);
                $this->parsingServices[] = $newService;
            } else {
                throw new Exception(
                    'Invalid class; instance of \T3forum\T3forum\TextParser\Service\AbstractTextParserService expected',
                    1315916625
                );
            }
        }
    }

    /**
     * Parses a certain input text.
     *
     * @param string $text The text that is to be parsed.
     * @return string The parsed text
     * @throws Exception
     */
    public function parseText($text)
    {
        if ($this->settings === null) {
            throw new Exception('The textparser is not configured!', 1284730639);
        }

        foreach ($this->parsingServices as &$parsingService) {
            /** @var $parsingService AbstractTextParserService */
            $text = $parsingService->getParsedText($text);
        }
        return $text;
    }
}
