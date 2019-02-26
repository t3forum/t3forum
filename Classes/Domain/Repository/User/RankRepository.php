<?php
namespace T3forum\T3forum\Domain\Repository\User;

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

use T3forum\T3forum\Domain\Model\User\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class RankRepository extends Repository
{
    /**
     * Find the rank of a specific user
     *
     * @param FrontendUser $user
     * @return \T3forum\T3forum\Domain\Model\User\Rank[]
     */
    public function findRankByUser(FrontendUser $user)
    {
        $query = $this->createQuery();
        $query->matching($query->lessThan('point_limit', $user->getPoints()));
        $query->setOrderings(['point_limit' => 'DESC']);
        $query->setLimit(1);
        return $query->execute();
    }

    /**
     * Find the rank for a given amount of points
     *
     * @param int $points
     * @deprecated
     * @return \T3forum\T3forum\Domain\Model\User\Rank[]
     */
    public function findRankByPoints($points)
    {
        $query = $this->createQuery();
        $query->matching($query->greaterThan('point_limit', (int)$points));
        $query->setOrderings(['point_limit' => 'ASC']);
        $query->setLimit(1);
        return $query->execute();
    }

    /**
     * Find one rank for a given amount of points
     *
     * @param int $points
     * @return \T3forum\T3forum\Domain\Model\User\Rank
     */
    public function findOneRankByPoints($points)
    {
        $query = $this->createQuery();
        $query->matching($query->greaterThan('point_limit', (int)$points));
        $query->setOrderings(['point_limit' => 'ASC']);
        $query->setLimit(1);
        $result = $query->execute();

        if ($result instanceof QueryResultInterface) {
            return $result->getFirst();
        } elseif (is_array($result)) {
            return isset($result[0]) ? $result[0] : null;
        }
        return null;
    }

    /**
     * Find all rankings for the ranking overview
     *
     * @return \T3forum\T3forum\Domain\Model\User\Rank[]
     */
    public function findAllForRankingOverview()
    {
        $query = $this->createQuery();
        $query->setOrderings(['point_limit' => 'ASC']);
        return $query->execute();
    }
}
