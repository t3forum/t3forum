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
use T3forum\T3forum\Domain\Repository\User\FrontendUserRepository;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;

/**
 * ViewHelper that renders a forum icon.
 */
class ForumIconViewHelper extends AbstractViewHelper
{
    /**
     *
     */
    protected $escapeOutput = false;

    /**
     * The frontend user repository.
     *
     * @var FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository = null;

    /**
     * Renders the forum icon.
     *
     * @param Forum $forum The forum for which the icon is to be rendered
     * @param int $width Image width
     * @param string $alt Alt text
     * @return string The rendered icon
     *
     */
    public function render(Forum $forum = null, $width = 0, $alt = '')
    {
        $data = $this->getDataArray($forum);
        $cObjectViewHelper = $this->getCObjectViewHelper();
        $typoscriptObjectPath = 'plugin.tx_t3forum.renderer.icons.forum';
        $typoscriptObjectPath .= $data['new'] ? '_new' : '';
        $cObjectViewHelper->arguments['typoscriptObjectPath'] = $typoscriptObjectPath;
        $cObjectViewHelper->arguments['data'] = $data;
        return $cObjectViewHelper->render($typoscriptObjectPath, $data);
    }

    /**
     * Generates a data array that will be passed to the typoscript object for
     * rendering the icon.
     *
     * @param Forum $forum The topic for which the icon is to be displayed.
     * @return array The data array for the typoscript object.
     */
    protected function getDataArray(Forum $forum = null)
    {
        if ($forum === null) {
            return [];
        } else {
            $user = &$this->frontendUserRepository->findCurrent();

            return [
                'new' => !$forum->hasBeenReadByUser($user),
                'closed' => !$forum->checkNewPostAccess($user),
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
