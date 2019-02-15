<?php
namespace T3forum\T3forum\Service;

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class InstallService
 */
class InstallService
{
    /**
     * @var string
     */
    private $extensionKey = 't3forum';

    /**
     * @var string
     */
    protected $messageQueueByIdentifier =
        'extbase.flashmessages.tx_extensionmanager_tools_extensionmanagerextensionmanager';

    /**
     * @param string $extensionKey
     */
    public function checkForMigrationOption($extensionKey = null)
    {
        if (($extensionKey === $this->extensionKey) && ($this->isUseful())) {
            /* @var FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                'Use update script for t3forum',
                'Use of mm_forum detected. You can use the update script of t3forum in extension manager',
                FlashMessage::NOTICE,
                true
            );
            $this->addFlashMessage($flashMessage);

            return;
        }
    }

    /**
     * @todo Implement database analyzer for mm_forum tables
     * @return bool
     */
    protected function isUseful()
    {
        return true;
    }

    /**
     * Adds a Flash Message to the Flash Message Queue
     *
     * @param FlashMessage $flashMessage
     * @return void
     */
    protected function addFlashMessage(FlashMessage $flashMessage)
    {
        if ($flashMessage) {
            /** @var $flashMessageService FlashMessageService */
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            /** @var $flashMessageQueue FlashMessageQueue */
            $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier($this->messageQueueByIdentifier);
            $flashMessageQueue->enqueue($flashMessage);
        }
    }
}
