<?php
namespace T3forum\T3forum\Domain\Model;

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
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use T3forum\T3forum\Domain\Model\Forum\Access;

/**
 * Interface definition for objects that are submitted to the access control
 * mechanism of the typo3_forum.
 */
interface AccessibleInterface
{
    /**
     * Checks if a certain frontend user has access to a specific operation.
     *
     * @param User\FrontendUser $user
     * @param string $accessType
     * @return bool
     */
    public function checkAccess(User\FrontendUser $user = null, $accessType = Access::TYPE_READ);
}
