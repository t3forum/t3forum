<?php
namespace T3forum\T3forum\Controller;

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

use T3forum\T3forum\Domain\Exception\Authentication\NotLoggedInException;
use T3forum\T3forum\Domain\Model\Forum\Forum;
use T3forum\T3forum\Domain\Model\Forum\RootForum;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use T3forum\T3forum\Domain\Repository\Forum\AdRepository;
use T3forum\T3forum\Domain\Repository\Forum\ForumRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ForumController extends AbstractUserAccessController
{
    /**
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository;

    /**
     * @var TopicRepository
     * @inject
     */
    protected $topicRepository;

    /**
     * @var AdRepository
     * @inject
     */
    protected $adRepository;

    /**
     * @var RootForum
     * @inject
     */
    protected $rootForum;

    /**
     * Index action. Displays the first two levels of the forum tree.
     * @return void
     */
    public function indexAction()
    {
        if (($forum = $this->forumRepository->findOneByForum(0))) {
            $this->forward(
                'show',
                'Forum',
                'T3Forum',
                ['forum' => $forum]
            );
        } else {
            // TODO: messsage or configurable or just nothing?
        }
    }

    /**
     * Show action. Displays a single forum, all subforums of this forum and the
     * topics contained in this forum.
     *
     * @param Forum $forum The forum that is to be displayed.
     * @return void
     */
    public function showAction(Forum $forum)
    {
        $topics = $this->topicRepository->findForIndex($forum);
        $this->authenticationService->assertReadAuthorization($forum);
        $this->view->assignMultiple([
            'forum' => $forum,
            'topics' => $topics,
        ]);
    }

    /**
     * Mark a whole forum as read
     *
     * @param Forum $forum
     * @throws NotLoggedInException
     * @return void
     */
    public function markReadAction(Forum $forum)
    {
        $user = $this->getCurrentUser();
        if (!$user instanceof FrontendUser || $user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in.', 1288084981);
        }
        $forumsToMarkAsRead = new ObjectStorage();
        $forumsToMarkAsRead->attach($forum);
        foreach ($forum->getChildren() as $child) {
            $forumsToMarkAsRead->attach($child);
        }

        foreach ($forumsToMarkAsRead as $checkForum) {
            /** @var Forum $checkForum */
            foreach ($checkForum->getTopics() as $topic) {
                /** @var Topic $topic */
                $topic->addReader($user);
            }
            $checkForum->addReader($user);
            $this->forumRepository->update($checkForum);
        }

        $this->redirect(
            'show',
            'Forum',
            null,
            ['forum' => $forum]
        );
    }

    /**
     * Show all unread topics of the current user
     *
     * @param Forum $forum
     * @throws NotLoggedInException
     * @return void
     */
    public function showUnreadAction(Forum $forum)
    {
        $user = $this->getCurrentUser();
        if (!$user instanceof FrontendUser || $user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in.', 1436620398);
        }
        $topics = [];
        $unreadTopics = [];

        $tmpTopics = $this->topicRepository->getUnreadTopics($forum, $user);
        foreach ($tmpTopics as $tmpTopic) {
            $unreadTopics[] = $tmpTopic['uid'];
        }
        if (!empty($unreadTopics)) {
            $topics = $this->topicRepository->findByUids($unreadTopics);
        }
        $this->view->assignMultiple([
            'forum' => $forum,
            'topics' => $topics,
        ]);
    }
}
