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

use T3forum\T3forum\Domain\Model\Forum\ShadowTopic;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Domain\Repository\User\FrontendUserRepository;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;

/**
 * ViewHelper that renders a topic icon.
 */
class TopicIconViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * The frontend user repository.
     *
     * @var FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository = null;

    /**
     * Initializes the view helper arguments.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'important',
            'integer',
            'Amount of posts required for a topic to contain in order to be marked as important',
            false,
            15
        );
    }

    /**
     * Renders the topic icon.
     *
     * @param Topic $topic The topic for which the icon is to be rendered.
     * @param int $width Image width
     * @return string The rendered icon.
     */
    public function render(Topic $topic = null, $width = null)
    {
        $data = $this->getDataArray($topic);
        $typoscriptObjectPath = 'plugin.tx_t3forum.renderer.icons.topic' . ($data['new'] ? '_new' : '');

        $cObjectViewHelper = $this->getCObjectViewHelper();
        $cObjectViewHelper->setArguments([
            'typoscriptObjectPath' => $typoscriptObjectPath,
            'data' => $data
        ]);
        return $cObjectViewHelper->render();
    }

    /**
     * Generates a data array that will be passed to the typoscript object for
     * rendering the icon.
     *
     * @param Topic $topic The topic for which the icon is to be displayed.
     * @return array The data array for the typoscript object.
     */
    protected function getDataArray(Topic $topic = null)
    {
        if ($topic === null) {
            return [];
        } elseif ($topic instanceof ShadowTopic) {
            return ['moved' => true];
        } else {
            $isImportant = $topic->getPostCount() >= $this->arguments['important'];

            return [
                'important' => $isImportant,
                'new' => !$topic->hasBeenReadByUser($this->frontendUserRepository->findCurrent()),
                'closed' => $topic->isClosed(),
                'sticky' => $topic->isSticky(),
                'solved' => $topic->getIsSolved(),
            ];
        }
    }

    /**
     * @return CObjectViewHelper
     */
    protected function getCObjectViewHelper()
    {
        return $this->objectManager->get(CObjectViewHelper::class);
    }
}
