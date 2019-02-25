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

use T3forum\T3forum\Domain\Model\SubscribeableInterface;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use T3forum\T3forum\Domain\Repository\User\FrontendUserRepository;
use TYPO3Fluid\Fluid\ViewHelpers\IfViewHelper;

/**
 * ViewHelper that renders its contents, when a certain user has subscribed
 * a specific object.
 */
class IfSubscribedViewHelper extends IfViewHelper
{
    /**
     * @var FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'object',
            SubscribeableInterface::class,
            'Object to check',
            true
        );
        $this->registerArgument(
            'user',
            FrontendUser::class,
            'className which object has to be',
            false,
            null
        );
    }

    /**
     * @return string
     */
    public function render()
    {
        $object = $this->getSubscribeableObject();
        $user = $this->arguments['user'];

        if ($user === null) {
            $user = $this->frontendUserRepository->findCurrent();
        }

        foreach ($object->getSubscribers() as $subscriber) {
            if ($subscriber->getUid() == $user->getUid()) {
                return $this->renderThenChild();
            }
        }

        return $this->renderElseChild();
    }

    /**
     * @return SubscribeableInterface
     */
    protected function getSubscribeableObject()
    {
        return $this->arguments['object'];
    }
}
