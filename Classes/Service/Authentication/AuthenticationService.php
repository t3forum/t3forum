<?php
namespace T3forum\T3forum\Service\Authentication;

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

use T3forum\T3forum\Domain\Exception\Authentication\NoAccessException;
use T3forum\T3forum\Domain\Model\AccessibleInterface;
use T3forum\T3forum\Domain\Model\Forum\Access;
use T3forum\T3forum\Domain\Model\Forum\Forum;
use T3forum\T3forum\Domain\Model\Forum\Post;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Service\AbstractService;
use T3forum\T3forum\Domain\Model\User\FrontendUserGroup;
use T3forum\T3forum\Domain\Repository\User\FrontendUserRepository;
use T3forum\T3forum\Cache\Cache;
use T3forum\T3forum\Domain\Model\User\FrontendUser;

/**
 * A service class that handles the entire authentication.
 */
class AuthenticationService extends AbstractService implements AuthenticationServiceInterface
{
    /**
     * @var FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository = null;

    /**
     * An instance of the typo3_forum cache class.
     * @var Cache
     * @inject
     */
    protected $cache = null;

    /**
     * The current frontend user.
     * @var FrontendUser
     */
    protected $user = -1;

    /**
     * An identifier for all user groups the current user is a member of.
     * This identifier will be used as part of a cache identifier.
     *
     * @var string
     */
    private $userGroupIdentifier = null;

    /**
     * Asserts that the current user is authorized to read a specific object.
     *
     * @param AccessibleInterface $object The object that is to be accessed.
     * @return void
     */
    public function assertReadAuthorization(AccessibleInterface $object)
    {
        $this->assertAuthorization($object, Access::TYPE_READ);
    }

    /**
     * Asserts that the current user is authorized to create a new topic in a
     * certain forum.
     *
     * @param Forum $forum The forum in which the new topic is to be created.
     * @return void
     */
    public function assertNewTopicAuthorization(Forum $forum)
    {
        $this->assertAuthorization($forum, Access::TYPE_NEW_TOPIC);
    }

    /**
     * Asserts that the current user is authorized to create a new post within a
     * topic.
     *
     * @param Topic $topic The topic in which the new post is to be created.
     * @return void
     */
    public function assertNewPostAuthorization(Topic $topic)
    {
        $this->assertAuthorization($topic, Access::TYPE_NEW_POST);
    }

    /**
     * Asserts that the current user is authorized to edit an existing post.
     *
     * @param Post $post The post that shall be edited.
     * @return void
     */
    public function assertEditPostAuthorization(Post $post)
    {
        $this->assertAuthorization($post, Access::TYPE_EDIT_POST);
    }

    /**
     * Asserts that the current user is authorized to delete a post.
     *
     * @param Post $post The post that is to be deleted.
     * @return void
     */
    public function assertDeletePostAuthorization(Post $post)
    {
        $this->assertAuthorization($post, Access::TYPE_DELETE_POST);
    }

    /**
     * Asserts that the current user has moderator access to a certain forum.
     *
     * @param AccessibleInterface $object The object that is to be moderated.
     * @return void
     */
    public function assertModerationAuthorization(AccessibleInterface $object)
    {
        $this->assertAuthorization($object, Access::TYPE_MODERATE);
    }

    /**
     * Asserts that the current user is authorized to perform a certain
     * action on an potentially protected object.
     *
     * @param AccessibleInterface $object The object for which the access is to be checked.
     * @param string $action The action for which the access check is to be performed.
     * @return void
     * @throws NoAccessException
     */
    public function assertAuthorization(AccessibleInterface $object, $action)
    {
        if ($this->checkAuthorization($object, $action) === false) {
            throw new NoAccessException('You are not authorized to perform this action!', 1284709852);
        }
    }

    /**
     * Checks whether the current user is authorized to perform a certain
     * action on an object.
     *
     * @param AccessibleInterface $object The object for which the access is to be checked.
     * @param string $action The action for which the access check is to be performed.
     * @return bool TRUE, when the user is authorized, otherwise FALSE.
     */
    public function checkAuthorization(AccessibleInterface $object, $action)
    {
        // ACLs can be disabled for debugging.
        if (isset($this->settings) && $this->settings['debug']['disableACLs']) {
            return true;
        }

        $cacheIdentifier = $this->getCacheIdentifier($object, $action);
        if ($this->cache->has($cacheIdentifier)) {
            $value = $this->cache->get($cacheIdentifier);
        } else {
            $this->cache->set($cacheIdentifier, $value = $object->checkAccess($this->getUser(), $action));
        }
        return $value;
    }

    /**
     * Gets the cache identifier to use for a specific user/object/action
     * check.
     * INTERNAL USE ONLY!
     *
     * @param AccessibleInterface $object The object for which the access is to be checked.
     * @param string $action The action for which the access check is to be performed.
     * @return string              The cache identifier.
     * @access private
     */
    protected function getCacheIdentifier(AccessibleInterface $object, $action)
    {
        $className = array_pop(explode('\\', get_class($object)));
        /** @noinspection PhpUndefinedMethodInspection */
        return 'acl-' . $className . '-' . $object->getUid() . '-' . $this->getUserGroupIdentifier() . '-' . $action;
    }

    /**
     * Generates an identifier for all user groups the current user is a member of. This identifier can then be used
     * as part of a cache identifier.
     *
     * @return string An identifier for all current user groups.
     */
    protected function getUserGroupIdentifier()
    {
        if ($this->userGroupIdentifier === null) {
            $user = $this->getUser();
            if ($user === null) {
                $this->userGroupIdentifier = 'n';
            } else {
                $groupUids = [];
                foreach ($user->getUsergroup() as $group) {
                    /** @var FrontendUserGroup $group */
                    $groupUids[] = $group->getUid();
                }
                $this->userGroupIdentifier = implode('g', $groupUids);
            }
        }
        return $this->userGroupIdentifier;
    }

    public function getUser()
    {
        if ($this->user === -1) {
            $this->user = $this->frontendUserRepository->findCurrent();
        }
        return $this->user;
    }
}
