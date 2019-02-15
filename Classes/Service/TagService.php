<?php
namespace T3forum\T3forum\Service;

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

use T3forum\T3forum\Domain\Model\Forum\Tag;
use T3forum\T3forum\Domain\Repository\Forum\TagRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class TagService implements SingletonInterface
{
    /**
     * An instance of the Extbase object manager.
     * @var ObjectManagerInterface
     * @inject
     */
    protected $objectManager = null;

    /**
     * An instance of the tag repository
     * @var TagRepository
     * @inject
     */
    protected $tagRepository;

    /**
     * Converts string of tags to an object
     *
     * @param string $tags
     *
     * @return ObjectStorage
     */
    public function initTags($tags)
    {
        /* @var $objTags Tag */
        $objTags = new ObjectStorage();

        $tagArray = array_unique(explode(',', $tags));
        foreach ($tagArray as $tagName) {
            $tagName = ucfirst(trim($tagName));
            if ($tagName === '') {
                continue;
            }
            $searchResult = $this->tagRepository->findTagWithSpecificName($tagName);
            if ($searchResult[0]) {
                $searchResult[0]->increaseTopicCount();
                $objTags->attach($searchResult[0]);
            } else {
                /* @var $tag Tag */
                $tag = $this->objectManager->get(Tag::class);
                $tag->setName($tagName);
                $tag->setCrdate(new \DateTime());
                $tag->increaseTopicCount();
                $objTags->attach($tag);
            }
        }
        return $objTags;
    }
}
