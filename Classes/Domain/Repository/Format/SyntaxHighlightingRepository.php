<?php
namespace T3forum\T3forum\Domain\Repository\Format;

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

use T3forum\T3forum\Domain\Repository\AbstractRepository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Repository class for syntax highlighting elements.
 */
class SyntaxHighlightingRepository extends AbstractRepository
{
    /**
     * @return QueryResultInterface
     */
    public function findAll()
    {
        $query = $this->createQueryWithFallbackStoragePage();
        return $query->execute();
    }
}
