<?php
namespace T3forum\T3forum\Domain\Validator\Forum;

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

class TagValidator extends AbstractValidator
{
    /**
     * An instance of the tag repository.
     * @var \T3forum\T3forum\Domain\Repository\Forum\TagRepository
     * @inject
     */
    protected $tagRepository;

    /**
     * Check if $value is valid. If it is not valid, needs to add an error
     * to Result.
     *
     * @param string $name
     * @return bool
     */
    protected function isValid($name = '')
    {
        $result = true;

        if (trim($name) === '') {
            $this->addError('The name can\'t be empty!.', 1373871955);
            $result = false;
        }
        $name = ucfirst($name);
        $res = $this->tagRepository->findTagWithSpecificName($name);
        if ($res[0] != false) {
            $this->addError('The tag already exists!.', 1373871960);
            $result = false;
        }

        return $result;
    }
}
