<?php
namespace T3forum\T3forum\ViewHelpers\Ext;

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

use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;
use Mittwald\Typo3Forum\Configuration\ConfigurationBuilder;
use Mittwald\Typo3Forum\Service\Authentication\AuthenticationServiceInterface;

class UserLinkViewHelper extends CObjectViewHelper
{
    /**
     * @var ConfigurationBuilder
     * @inject
     */
    protected $configurationBuilder;

    /**
     * Whole TypoScript typo3_forum settings
     *
     * @var array
     */
    protected $settings;

    /**
     * An authentication service. Handles the authentication mechanism.
     *
     * @var AuthenticationServiceInterface
     * @inject
     */
    protected $authenticationService = null;

    public function initializeObject()
    {
        $this->settings = $this->configurationBuilder->getSettings();
    }

    public function initialize()
    {
        parent::initialize();
    }

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('class', 'string', 'CSS class.');
        $this->registerArgument('style', 'string', 'CSS inline styles.');
    }

    /**
     * render
     *
     * @param bool|TRUE $link
     * @return string
     */
    public function render($link = true)
    {
        $user = $this->authenticationService->getUser();
        if ($link) {
            $uriBuilder = $this->controllerContext->getUriBuilder();
            $uri = $uriBuilder->setTargetPageUid($this->settings['pids']['UserShow'])->setArguments([
                'tx_t3forum_pi1[user]' => $user->getUid(),
                'tx_t3forum_pi1[controller]' => 'User',
                'tx_t3forum_pi1[action]' => 'show'
            ])->build();
            return '<a href="' . $uri . '" title="' . $user->getUsername() . '">' . $user->getUsername() . '</a>';
        } else {
            return $user->getUsername();
        }
    }
}
