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
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

use T3forum\T3forum\Domain\Factory\Forum\TopicFactory;
use T3forum\T3forum\Domain\Factory\Forum\TopicFactory;
use T3forum\T3forum\Domain\Model\Forum\Forum;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Domain\Model\Moderation\PostReport;
use T3forum\T3forum\Domain\Model\Moderation\Report;
use T3forum\T3forum\Domain\Model\Moderation\ReportComment;
use T3forum\T3forum\Domain\Model\Moderation\ReportWorkflowStatus;
use T3forum\T3forum\Domain\Model\Moderation\UserReport;
use T3forum\T3forum\Domain\Repository\Forum\ForumRepository;
use T3forum\T3forum\Domain\Repository\Forum\PostRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use T3forum\T3forum\Domain\Repository\Moderation\PostReportRepository;
use T3forum\T3forum\Domain\Repository\Moderation\ReportRepository;
use T3forum\T3forum\Domain\Repository\Moderation\UserReportRepository;
use T3forum\T3forum\Domain\Repository\Moderation\UserReportRepository;
use T3forum\T3forum\Utility\Localization;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 *
 */
class ModerationController extends AbstractUserAccessController
{

    /**
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository;

    /**
     * @var PersistenceManagerInterface
     * @inject
     */
    protected $persistenceManager;

    /**
     * @var PostReportRepository
     * @inject
     */
    protected $postReportRepository = null;

    /**
     * @var PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * @var ReportRepository
     * @inject
     */
    protected $reportRepository = null;

    /**
     * @var TopicFactory
     * @inject
     */
    protected $topicFactory;

    /**
     * @var TopicRepository
     * @inject
     */
    protected $topicRepository;

    /**
     * @var UserReportRepository
     * @inject
     */
    protected $userReportRepository = null;

    /**
     * @return void
     */
    public function indexReportAction()
    {
        $this->view->assign('postReports', $this->postReportRepository->findAll());
        $this->view->assign('userReports', $this->userReportRepository->findAll());
    }

    /**
     * editReportAction
     *
     * @param UserReport $userReport
     * @param PostReport|NULL $postReport
     *
     * @return void
     * @throws InvalidArgumentValueException
     */
    public function editReportAction(UserReport $userReport = null, PostReport $postReport = null)
    {
        // Validate arguments
        if ($userReport === null && $postReport === null) {
            throw new InvalidArgumentValueException('You need to show a user report or post report!', 1285059341);
        }
        if ($postReport) {
            $report = $postReport;
            $type = 'Post';
            $this->authenticationService->assertModerationAuthorization($postReport->getTopic()->getForum());
        } else {
            $type = 'User';
            $report = $userReport;
        }

        $this->view->assignMultiple([
            'report' => $report,
            'type' => $type,
        ]);
    }

    /**
     * @param Report $report
     * @param ReportComment $comment
     *
     * @dontvalidate $comment
     */
    public function newReportCommentAction(Report $report, ReportComment $comment = null)
    {
        $this->view->assignMultiple([
            'comment' => $comment,
            'report' => $report,
        ]);
    }

    /**
     * @param UserReport $report
     * @param ReportComment $comment
     * @return void
     * @throws InvalidArgumentValueException
     */
    public function createUserReportCommentAction(UserReport $report = null, ReportComment $comment)
    {

        // Validate arguments
        if ($report === null) {
            throw new InvalidArgumentValueException('You need to comment a user report!', 1285059341);
        }

        $comment->setAuthor($this->authenticationService->getUser());
        $report->addComment($comment);
        $this->reportRepository->update($report);

        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Report_NewComment_Success'))
        );

        $this->clearCacheForCurrentPage();
        $this->redirect('editReport', null, null, ['userReport' => $report]);
    }

    /**
     * Create post report comment action
     *
     * @param PostReport|NULL $report
     * @param ReportComment $comment
     *
     * @return void
     * @throws InvalidArgumentValueException
     * @throws IllegalObjectTypeException
     */
    public function createPostReportCommentAction(PostReport $report = null, ReportComment $comment)
    {
        // Assert authorization
        $this->authenticationService->assertModerationAuthorization($report->getTopic()->getForum());

        // Validate arguments
        if ($report === null) {
            throw new InvalidArgumentValueException('You need to comment a user report!', 1285059341);
        }

        $comment->setAuthor($this->authenticationService->getUser());
        $report->addComment($comment);
        $this->reportRepository->update($report);

        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Report_NewComment_Success'))
        );

        $this->clearCacheForCurrentPage();
        $this->redirect('editReport', null, null, ['postReport' => $report]);
    }

    /**
     * Sets the workflow status of a report
     *
     * @param UserReport $report
     * @param ReportWorkflowStatus $status
     * @param string $redirect
     * @throws IllegalObjectTypeException
     * @return void
     */
    public function updateUserReportStatusAction(
        UserReport $report,
        ReportWorkflowStatus $status,
        $redirect = 'indexReport'
    ) {
        // Set status and update the report. Add a comment to the report that
        // documents the status change.
        $report->setWorkflowStatus($status);
        /** @var ReportComment $comment */
        $comment = GeneralUtility::makeInstance(ReportComment::class);
        $comment->setAuthor($this->getCurrentUser());
        $comment->setText(Localization::translate('Report_Edit_SetStatus', 'T3forum', [$status->getName()]));
        $report->addComment($comment);
        $this->reportRepository->update($report);

        // Add flash message and clear cache.
        $this->addLocalizedFlashmessage('Report_UpdateStatus_Success', [$report->getUid(), $status->getName()]);
        $this->clearCacheForCurrentPage();

        if ($redirect === 'show') {
            $this->redirect('editReport', null, null, ['userReport' => $report]);
        }

        $this->redirect('indexReport');
    }

    /**
     * Sets the workflow status of a report.
     *
     * @param PostReport $report
     * @param ReportWorkflowStatus $status
     * @param string $redirect
     */
    public function updatePostReportStatusAction(
        PostReport $report,
        ReportWorkflowStatus $status,
        $redirect = 'indexReport'
    ) {
        // Assert authorization
        $this->authenticationService->assertModerationAuthorization($report->getTopic()->getForum());

        // Set status and update the report. Add a comment to the report that
        // documents the status change.
        $report->setWorkflowStatus($status);
        /** @var ReportComment $comment */
        $comment = GeneralUtility::makeInstance(ReportComment::class);
        $comment->setAuthor($this->getCurrentUser());
        $comment->setText(Localization::translate('Report_Edit_SetStatus', 'T3forum', [$status->getName()]));
        $report->addComment($comment);
        $this->reportRepository->update($report);

        // Add flash message and clear cache.
        $this->addLocalizedFlashmessage('Report_UpdateStatus_Success', [$report->getUid(), $status->getName()]);
        $this->clearCacheForCurrentPage();

        if ($redirect === 'show') {
            $this->redirect('editReport', null, null, ['postReport' => $report]);
        }

        $this->redirect('indexReport');
    }

    /**
     * Displays a form for editing a topic with special moderator-powers!
     *
     * @param Topic $topic The topic that is to be edited.
     */
    public function editTopicAction(Topic $topic)
    {
        $this->authenticationService->assertModerationAuthorization($topic->getForum());
        $this->view->assign('topic', $topic);
    }

    /**
     * Updates a forum with special super-moderator-powers!
     *
     * @param Topic $topic The topic that is be edited.
     * @param bool $moveTopic TRUE, if the topic is to be moved to another forum.
     * @param Forum $moveTopicTarget The forum to which the topic is to be moved.
     */
    public function updateTopicAction(Topic $topic, $moveTopic = false, Forum $moveTopicTarget = null)
    {
        $this->authenticationService->assertModerationAuthorization($topic->getForum());
        $this->topicRepository->update($topic);

        if ($moveTopic) {
            $this->topicFactory->moveTopic($topic, $moveTopicTarget);
        }

        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Moderation_UpdateTopic_Success', 'T3forum'))
        );
        $this->clearCacheForCurrentPage();
        $this->redirect('show', 'Topic', null, ['topic' => $topic]);
    }

    /**
     * Delete a topic from repository!
     *
     * @param Topic $topic The topic that is be deleted.
     * @return void
     */
    public function topicConformDeleteAction(Topic $topic)
    {
        $this->authenticationService->assertModerationAuthorization($topic->getForum());
        foreach ($topic->getPosts() as $post) {
            $this->postRepository->remove($post);
        }
        $this->topicRepository->remove($topic);
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Moderation_DeleteTopic_Success', 'T3forum'))
        );
        $this->clearCacheForCurrentPage();

        $this->redirect('show', 'Forum', null, ['forum' => $topic->getForum()]);
    }
}
