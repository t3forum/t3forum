<?php
namespace T3forum\T3forum\Domain\Validator\User;

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

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 *
 * A validator class for author names. This class validates a username ONLY if
 * no user is currently logged in.
 *
 * @author     Ruven Fehling <r.fehling@mittwald.de>
 * @version    $Id$
 *
 * @license    GNU Public License, version 2
 *             http://opensource.org/licenses/gpl-license.php
 *
 */
class PrivateMessageRecipientValidator extends AbstractValidator
{
    /**
     * @var \T3forum\T3forum\Domain\Repository\User\FrontendUserRepository
     * @inject
     */
    protected $userRepository = null;

    /**
     * Check if $value is valid. If it is not valid, needs to add an error
     * to Result.
     *
     * @param $value
     * @return bool
     */
    protected function isValid($value)
    {
        $result = true;

        if (!$this->userRepository->findOneByUsername($value)) {
            $this->addError('PM reciepient user not found!', 1372429326);
            $result = false;
        }
        $user = $this->userRepository->findCurrent();
        if ($user->getUsername() == $value) {
            $this->addError('You can\'t write yourself a message :)', 1372682275);
            $result = false;
        }

        return $result;
    }
}
