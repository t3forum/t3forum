<?php

namespace T3forum\T3forum\Domain\Model\User;

/*                                                                    - *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2015 Mittwald CM Service GmbH & Co KG                           *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class PrivateMessageText extends AbstractEntity
{

    /**
     * The submitted text
     * @var string
     */
    public $messageText;

    /**
     * Get the short text of this pm
     * @return string The short text
     */
    public function getShortMessageText()
    {
        $limit = 80;
        $text = $this->getMessageText();
        if (strlen($text) < $limit) {
            return $text;
        } else {
            return substr($text, 0, $limit) . '...';
        }
    }

    /**
     * Get the text of this pm
     * @return string The text
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * Sets the text
     *
     * @param string $messageText
     *
     * @return void
     */
    public function setMessageText($messageText)
    {
        $this->messageText = $messageText;
    }
}
