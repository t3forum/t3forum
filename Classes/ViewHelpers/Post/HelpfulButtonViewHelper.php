<?php
namespace T3forum\T3forum\ViewHelpers\Post;

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
use T3forum\T3forum\Domain\Repository\User\FrontendUserRepository;
use T3forum\T3forum\Service\Authentication\AuthenticationServiceInterface;
use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;

class HelpfulButtonViewHelper extends CObjectViewHelper
{
    /**
     * @var array
     */
    protected $settings = null;

    /**
     * The frontend user repository.
     *
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository = null;

    /**
     * An authentication service. Handles the authentication mechanism.
     *
     * @var AuthenticationServiceInterface
     * @inject
     */
    protected $authenticationService;

    public function initialize()
    {
        parent::initialize();
        $this->settings = $this->templateVariableContainer->get('settings');
    }

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('class', 'string', 'CSS class.');
    }

    /**
     * @param Post $post
     * @param string $countTarget
     * @param string $countUserTarget
     * @param string $title
     * @return string
     */
    public function render(Post $post, $countTarget = null, $countUserTarget = null, $title = '')
    {
        $class = $this->settings['forum']['post']['helpfulBtn']['iconClass'];

        if ($this->hasArgument('class')) {
            $class .= ' ' . $this->arguments['class'];
        }
        if ($post->getAuthor()->getUid() != $this->authenticationService->getUser()->getUid()
         && !$this->authenticationService->getUser()->isAnonymous()) {
            $class .= ' tx-t3forum-helpfull-btn';
        }

        if ($post->hasBeenSupportedByUser($this->authenticationService->getUser())) {
            $class .= ' supported';
        }
        $btn = '<div data-toogle="tooltip" title="' . $title . '" data- class="' . $class .
            '" data-countusertarget="' . $countUserTarget . '" data-counttarget="' . $countTarget .
            '" data-post="' . $post->getUid() . '" data-pageuid="' . $this->settings['pids']['Forum'] .
            '" data-eid="' . $this->settings['forum']['post']['helpfulBtn']['eID'] . '"></div>';
        return $btn;
    }
}
