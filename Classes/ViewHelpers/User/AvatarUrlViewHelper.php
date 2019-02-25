<?php
namespace T3forum\T3forum\ViewHelpers\User;

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

use T3forum\T3forum\Domain\Model\User\AnonymousFrontendUser;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * ViewHelper that renders a user's avatar.
 */
class AvatarUrlViewHelper extends CObjectViewHelper
{
    /**
     * An instance of the Extbase Signal-/Slot-Dispatcher.
     *
     * @var Dispatcher
     * @inject
     */
    protected $slots;

    /**
     * Initializes the view helper's arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
    }

    /**
     * Renders the avatar.
     *
     * @param FrontendUser $user
     * @return string
     */
    public function render(FrontendUser $user = null)
    {
        // if user ist not set
        $avatarFilename = null;

        if (($user !== null) && !($user instanceof AnonymousFrontendUser)) {
            $avatarFilename = $user->getImagePath();
        }

        if ($avatarFilename === null) {
            $avatarFilename = ExtensionManagementUtility::siteRelPath('t3forum') .
                'Resources/Public/Images/Icons/AvatarEmpty.png';
        }
        return $avatarFilename;
    }
}
