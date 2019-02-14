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

use T3forum\T3forum\Domain\Factory\Moderation\ReportFactory;
use T3forum\T3forum\Domain\Model\Forum\Post;
use T3forum\T3forum\Domain\Model\Moderation\PostReport;
use T3forum\T3forum\Domain\Model\Moderation\Report;
use T3forum\T3forum\Domain\Model\Moderation\ReportComment;
use T3forum\T3forum\Domain\Model\Moderation\UserReport;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use T3forum\T3forum\Domain\Repository\Moderation\PostReportRepository;
use T3forum\T3forum\Domain\Repository\Moderation\ReportRepository;
use T3forum\T3forum\Domain\Repository\Moderation\UserReportRepository;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 *
 */
class ReportController extends AbstractUserAccessController
{
    /**
     * @var PostReportRepository
     * @inject
     */
    protected $postReportRepository;

    /**
     * @var ReportFactory
     * @inject
     */
    protected $reportFactory;

    /**
     * @var ReportRepository
     * @inject
     */
    protected $reportRepository;

    /**
     * @var UserReportRepository
     * @inject
     */
    protected $userReportRepository;

    /**
     * Displays a form for creating a new post report.
     *
     * @param FrontendUser $user
     * @param ReportComment $firstComment
     *
     * @dontvalidate $firstComment
     */
    public function newUserReportAction(FrontendUser $user, ReportComment $firstComment = null)
    {
        $this->view->assignMultiple([
            'firstComment' => $firstComment,
            'user' => $user,
        ]);
    }

    /**
     * Displays a form for creating a new post report.
     *
     * @param Post $post
     * @param ReportComment $firstComment
     *
     * @dontvalidate $firstComment
     */
    public function newPostReportAction(Post $post, ReportComment $firstComment = null)
    {
        $this->authenticationService->assertReadAuthorization($post);
        $this->view->assignMultiple([
            'firstComment' => $firstComment,
            'post' => $post
        ]);
    }

    /**
     * Creates a new post report and stores it into the database.
     *
     * @param FrontendUser $user
     * @param ReportComment $firstComment
     */
    public function createUserReportAction(FrontendUser $user, ReportComment $firstComment = null)
    {
        /** @var UserReport $report */
        $report = $this->reportFactory->createUserReport($firstComment);
        $report->setUser($user);
        $this->userReportRepository->add($report);

        // Notify observers.
        $this->signalSlotDispatcher->dispatch(
            Report::class,
            'reportCreated',
            ['report' => $report]
        );

        // Display success message and redirect to topic->show action.
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(LocalizationUtility::translate('Report_New_Success', 'T3forum'))
        );
        $this->redirect(
            'show',
            'User',
            null,
            ['user' => $user],
            $this->settings['pids']['UserShow']
        );
    }

    /**
     * Creates a new post report and stores it into the database.
     *
     * @param Post $post
     * @param ReportComment $firstComment
     */
    public function createPostReportAction(Post $post, ReportComment $firstComment = null)
    {
        // Assert authorization;
        $this->authenticationService->assertReadAuthorization($post);

        /** @var PostReport $report */
        $report = $this->reportFactory->createPostReport($firstComment);
        $report->setPost($post);
        $this->postReportRepository->add($report);

        // Notify observers.
        $this->signalSlotDispatcher->dispatch(
            Report::class,
            'reportCreated',
            ['report' => $report]
        );

        // Display success message and redirect to topic->show action.
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(LocalizationUtility::translate('Report_New_Success', 'T3forum'))
        );
        $this->redirect(
            'show',
            'Topic',
            null,
            ['topic' => $post->getTopic()]
        );
    }
}
