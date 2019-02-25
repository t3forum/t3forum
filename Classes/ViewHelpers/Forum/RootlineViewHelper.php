<?php
namespace T3forum\T3forum\ViewHelpers\Forum;

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

use T3forum\T3forum\Domain\Model\Forum\Forum;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper that renders a big button.
 */
class RootlineViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'ul';

    /**
     * @var array
     */
    protected $settings = null;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
    }

    public function initialize()
    {
        parent::initialize();
        $this->settings = $this->templateVariableContainer->get('settings');
    }

    /**
     * render
     *
     * @param array $rootline
     * @param bool|FALSE $reverse
     * @return string
     */
    public function render(array $rootline, $reverse = false)
    {
        if ($reverse) {
            array_reverse($rootline);
        }

        $class = 'nav nav-pills nav-pills-condensed';
        if ($this->arguments['class']) {
            $class .= ' ' . $this->arguments['class'];
        }
        $this->tag->addAttribute('class', $class);

        $content = '';
        foreach ($rootline as $element) {
            $content .= $this->renderNavigationNode($element);
        }
        $content .= '';

        $this->tag->setContent($content);
        return $this->tag->render();
    }

    /**
     * renderNavigationNode
     *
     * @param $object
     * @return string
     */
    protected function renderNavigationNode($object)
    {
        $extensionName = 'typo3forum';
        $pluginName = 'pi1';
        if ($object instanceof Forum) {
            $controller = 'Forum';
            $arguments = ['forum' => $object];
            $icon = 'iconset-22-folder';
        } else {
            $controller = 'Topic';
            $arguments = ['topic' => $object];
            $icon = 'iconset-22-balloon';
        }
        $fullTitle = htmlspecialchars($object->getTitle());
        $limit = (int)$this->settings['cutBreadcrumbOnChar'];
        if ($limit == 0 || strlen($fullTitle) < $limit) {
            $title = $fullTitle;
        } else {
            $title = substr($fullTitle, 0, $limit) . '...';
        }

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uri = $uriBuilder->reset()->setTargetPageUid((int)$this->settings['pids']['Forum'])
            ->uriFor('show', $arguments, $controller, $extensionName, $pluginName);

        return '
            <li>
            <a href="' . $uri . '" title="' . $fullTitle . '">
            <i class="' . $icon . '"></i>' . $title . '</a>
            </li>';
    }
}
