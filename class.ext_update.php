<?php
namespace T3forum\T3forum;

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

use T3forum\T3forum\Service\Migration\AttachmentMigrationService;
use T3forum\T3forum\Service\Migration\ForumMigrationService;
use T3forum\T3forum\Service\Migration\PostsMigrationService;
use T3forum\T3forum\Service\Migration\PrivateMessageMigrationService;
use T3forum\T3forum\Service\Migration\TopicsMigrationService;
use T3forum\T3forum\Service\Migration\UserGroupMigrationService;
use T3forum\T3forum\Service\Migration\UserMigrationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ext_update
 *
 */
class ext_update
{
    /**
     * @var \T3forum\T3forum\Service\Migration\AbstractMigrationService[]
     */
    private $services = [];

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * ext_update constructor.
     */
    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->services = [
            $objectManager->get(ForumMigrationService::class),
            $objectManager->get(TopicsMigrationService::class),
            $objectManager->get(PostsMigrationService::class),
            $objectManager->get(AttachmentMigrationService::class),
            $objectManager->get(UserMigrationService::class),
            $objectManager->get(UserGroupMigrationService::class),
            $objectManager->get(PrivateMessageMigrationService::class),
        ];

        $this->objectManager = $objectManager;
    }

    /**
     * @return bool
     */
    public function access()
    {
        return true;
    }

    /**
     * Main method which will be called every time
     *
     * @return string
     */
    public function main()
    {
        if ((GeneralUtility::_POST('update') == true) && ($userPid = GeneralUtility::_POST('user_pid'))) {
            return $this->processUpdates();
        }

        return $this->renderForm();
    }

    /**
     * @return string
     */
    protected function processUpdates()
    {
        $output = '';
        foreach ($this->services as $service) {
            $output .= $service->migrate();
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function renderForm()
    {
        /* @var $partial \TYPO3\CMS\Fluid\View\StandaloneView */
        $partial = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
        $partial->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:t3forum/Resources/Private/Templates/Backend/Update/Form.html'
            )
        );
        $partial->assign(
            'action',
            GeneralUtility::getIndpEnv('REQUEST_URI')
        );

        return $partial->render();
    }
}
