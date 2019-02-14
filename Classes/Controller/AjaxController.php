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

use T3forum\T3forum\Configuration\ConfigurationBuilder;
use T3forum\T3forum\Domain\Factory\Forum\PostFactory;
use T3forum\T3forum\Domain\Model\Forum\Post;
use T3forum\T3forum\Domain\Repository\Forum\AdRepository;
use T3forum\T3forum\Domain\Repository\Forum\AttachmentRepository;
use T3forum\T3forum\Domain\Repository\Forum\ForumRepository;
use T3forum\T3forum\Domain\Repository\Forum\PostRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use T3forum\T3forum\Service\AttachmentService;
use T3forum\T3forum\Service\SessionHandlingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 *
 */
class AjaxController extends AbstractUserAccessController
{

    /**
     * @var AdRepository
     * @inject
     */
    protected $adRepository;

    /**
     * @var AttachmentRepository
     * @inject
     */
    protected $attachmentRepository;

    /**
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository;

    /**
     * @var ConfigurationBuilder
     * @inject
     */
    protected $configurationBuilder;

    /**
     * @var PostFactory
     * @inject
     */
    protected $postFactory;

    /**
     * @var PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * Entire TypoScript t3forum settings
     * @var array
     */
    protected $settings;

    /**
     * @var TopicRepository
     * @inject
     */
    protected $topicRepository;

    /**
     * @var SessionHandlingService
     * @inject
     */
    protected $sessionHandlingService;

    /**
     * @var AttachmentService
     * @inject
     */
    protected $attachmentService = null;

    /**
     *
     */
    public function initializeObject()
    {
        $this->settings = $this->configurationBuilder->getSettings();
    }

    /**
     * @param string $displayedUser
     * @param string $postSummarys
     * @param string $topicIcons
     * @param string $forumIcons
     * @param string $displayedTopics
     * @param int    $displayOnlinebox
     * @param string $displayedPosts
     * @param string $displayedForumMenus
     * @param string $displayedAds
     * @return void
     */
    public function mainAction(
        $displayedUser = '',
        $postSummarys = '',
        $topicIcons = '',
        $forumIcons = '',
        $displayedTopics = '',
        $displayOnlinebox = 0,
        $displayedPosts = '',
        $displayedForumMenus = '',
        $displayedAds = ''
    ) {
        $content = [];
        if (!empty($displayedUser)) {
            $content['onlineUser'] = $this->_getOnlineUser($displayedUser);
        }
        if (!empty($displayedForumMenus)) {
            $content['forumMenus'] = $this->_getForumMenus($displayedForumMenus);
        }
        if (!empty($postSummarys)) {
            $content['postSummarys'] = $this->_getPostSummarys($postSummarys);
        }
        if (!empty($topicIcons)) {
            $content['topicIcons'] = $this->_getTopicIcons($topicIcons);
        }
        if (!empty($forumIcons)) {
            $content['forumIcons'] = $this->_getForumIcons($forumIcons);
        }
        if (!empty($displayedTopics)) {
            $content['topics'] = $this->_getTopics($displayedTopics);
        }
        if (!empty($displayedPosts)) {
            $content['posts'] = $this->_getPosts($displayedPosts);
        }
        if (!empty($displayedPosts)) {
            $content['posts'] = $this->_getPosts($displayedPosts);
        }
        if ($displayOnlinebox == 1) {
            $content['onlineBox'] = $this->_getOnlinebox();
        }
        $displayedAds = json_decode($displayedAds);
        if ((int)$displayedAds->count > 1) {
            $content['ads'] = $this->_getAds($displayedAds);
        }
        return json_encode($content);
    }

    /**
     * @return void
     */
    public function loginboxAction()
    {
        $this->view->assign('user', $this->getCurrentUser());
    }

    /**
     * @return string
     */
    private function getTemplatePath()
    {
        $templatePaths = $this->view->getTemplatePaths()->getTemplateRootPaths();
        return array_pop($templatePaths);
    }

    /**
     * @param string $templateSubPath    filepath starting from templateRootPath
     * @return string
     */
    private function getStandaloneView($templateSubPath)
    {
        $templateSubPath = substr($templateSubPath, 0, 1)!=='/' ? '/' . $templateSubPath : $templateSubPath;
        /* @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename($this->getTemplatePath() . $templateSubPath);
        $standaloneView->setControllerContext($this->controllerContext);
        return $standaloneView;
    }

    /**
     * @return array
     */
    private function _getOnlinebox()
    {
        $data = [];
        $standaloneView = $this->getStandaloneView('/Ajax/Onlinebox.html');
        $users = $this->frontendUserRepository->findByFilter(
            intval($this->settings['widgets']['onlinebox']['limit']),
            [],
            true
        );
        $standaloneView->assign('users', $users);
        $data['count'] = $this->frontendUserRepository->countByFilter(true);
        $data['html'] = $this->view->render('Onlinebox');
        return $data;
    }

    /**
     * @param string $displayedForumMenus
     * @return array
     */
    private function _getForumMenus($displayedForumMenus)
    {
        $data = [];
        $displayedForumMenus = json_decode($displayedForumMenus);
        // If no forumMenus are requested return empty array
        if (count($displayedForumMenus) < 1) {
            return $data;
        }
        $standaloneView = $this->getStandaloneView('/Ajax/ForumMenu.html');
        $foren = $this->forumRepository->findByUids($displayedForumMenus);
        $counter = 0;
        foreach ($foren as $forum) {
            $standaloneView->assignMultiple([
                'forum' => $forum,
                'user' => $this->getCurrentUser()
            ]);
            $data[$counter]['uid'] = $forum->getUid();
            $data[$counter]['html'] = $standaloneView->render();
            $counter++;
        }
        return $data;
    }

    /**
     * @param string $displayedTopics
     * @return array
     */
    private function _getTopics($displayedTopics)
    {
        $data = [];
        $displayedTopics = json_decode($displayedTopics);
        if (count($displayedTopics) < 1) {
            return $data;
        }
        $standaloneView = $this->getStandaloneView('/Ajax/TopicListMenu.html');
        $topicIcons = $this->topicRepository->findByUids($displayedTopics);
        $counter = 0;
        foreach ($topicIcons as $topic) {
            $standaloneView->assign('topic', $topic);
            $data[$counter]['uid'] = $topic->getUid();
            $data[$counter]['replyCount'] = $topic->getReplyCount();
            $data[$counter]['topicListMenu'] = $standaloneView->render();
            $counter++;
        }
        return $data;
    }

    /**
     * @param string $topicIcons
     * @return array
     */
    private function _getTopicIcons($topicIcons)
    {
        $data = [];
        $topicIcons = json_decode($topicIcons);
        if (count($topicIcons) < 1) {
            return $data;
        }
        $standaloneView = $this->getStandaloneView('/Ajax/topicIcon.html');
        $topicIcons = $this->topicRepository->findByUids($topicIcons);
        $counter = 0;
        foreach ($topicIcons as $topic) {
            $standaloneView->assign('topic', $topic);
            $data[$counter]['html'] = $this->view->render('topicIcon');
            $data[$counter]['uid'] = $topic->getUid();
            $counter++;
        }
        return $data;
    }

    /**
     * @param string $forumIcons
     * @return array
     */
    private function _getForumIcons($forumIcons)
    {
        $data = [];
        $forumIcons = json_decode($forumIcons);
        if (count($forumIcons) < 1) {
            return $data;
        }
        $standaloneView = $this->getStandaloneView('/Ajax/forumIcon.html');
        $forumIcons = $this->forumRepository->findByUids($forumIcons);
        $counter = 0;
        foreach ($forumIcons as $forum) {
            $standaloneView->assign('forum', $forum);
            $data[$counter]['html'] = $this->view->render('forumIcon');
            $data[$counter]['uid'] = $forum->getUid();
            $counter++;
        }
        return $data;
    }

    /**
     * @param string $postSummarys
     * @return array
     */
    private function _getPostSummarys($postSummarys)
    {
        $postSummarys = json_decode($postSummarys);
        $data = [];
        $counter = 0;
        $standaloneView = $this->getStandaloneView('/Ajax/postSummary.html');
        foreach ($postSummarys as $summary) {
            $post = false;
            switch ($summary->type) {
                case 'lastForumPost':
                    $forum = $this->forumRepository->findByUid($summary->uid);
                    /* @var Post */
                    $post = $forum->getLastPost();
                    break;
                case 'lastTopicPost':
                    $topic = $this->topicRepository->findByUid($summary->uid);
                    /* @var Post */
                    $post = $topic->getLastPost();
                    break;
            }
            if ($post) {
                $data[$counter] = $summary;
                $standaloneView->assignMultiple([
                    'post' => $post,
                    'hiddenImage' => $summary->hiddenimage
                ]);
                $data[$counter]->html = $standaloneView->render('postSummary');
                $counter++;
            }
        }
        return $data;
    }

    /**
     * @param \stdClass $meta
     * @return array
     */
    private function _getAds(\stdClass $meta)
    {
        $count = (int)$meta->count;
        $result = [];
        $standaloneView = $this->getStandaloneView('/Ajax/Ads.html');

        $actDatetime = new \DateTime();
        if (!$this->sessionHandlingService->get('adTime')) {
            $this->sessionHandlingService->set('adTime', $actDatetime);
            $adDateTime = $actDatetime;
        } else {
            $adDateTime = $this->sessionHandlingService->get('adTime');
        }

        $actTimestamp = $actDatetime->getTimestamp();
        $adTimestamp = $adDateTime->getTimestamp();
        if ($actTimestamp - $adTimestamp > $this->settings['ads']['timeInterval'] && $count > 2) {
            $this->sessionHandlingService->set('adTime', $actDatetime);
            if ((int)$meta->mode === 0) {
                $ads = $this->adRepository->findForForumView(1);
            } else {
                $ads = $this->adRepository->findForTopicView(1);
            }
            if (!empty($ads)) {
                $standaloneView->assign('ads', $ads);
                $result['position'] = mt_rand(1, $count - 2);
                $result['html'] = $standaloneView->render('ads');
            }
        }
        return $result;
    }

    /**
     * @param string $displayedPosts
     * @return array
     */
    private function _getPosts($displayedPosts)
    {
        //DebuggerUtility::var_dump($displayedPosts,__METHOD__);
        $data = [];
        $displayedPosts = json_decode($displayedPosts);
        if (count($displayedPosts) < 1) {
            return $data;
        }
        $standaloneViews = [
            'PostHelpfulButton' => $this->getStandaloneView('/Ajax/PostHelpfulButton.html'),
            'PostEditLink' => $this->getStandaloneView('/Ajax/PostEditLink.html')
        ];
        $posts = $this->postRepository->findByUids($displayedPosts);
        $counter = 0;
        foreach ($posts as $post) {
            $standaloneViews['PostHelpfulButton']->assignMultiple([
                'post' => $post,
                'user' => $this->getCurrentUser()
            ]);
            $standaloneViews['PostEditLink']->assignMultiple([
                'post' => $post,
                'user' => $this->getCurrentUser()
            ]);
            $data[$counter]['uid'] = $post->getUid();
            $data[$counter]['postHelpfulButton'] = $standaloneViews['PostHelpfulButton']->render();
            $data[$counter]['postHelpfulCount'] = $post->getHelpfulCount();
            $data[$counter]['postUserHelpfulCount'] = $post->getAuthor()->getHelpfulCount();
            $data[$counter]['author']['uid'] = $post->getAuthor()->getUid();
            $data[$counter]['postEditLink'] = $standaloneViews['PostEditLink']->render();
            $counter++;
        }
        //DebuggerUtility::var_dump($data,__METHOD__);
        return $data;
    }

    /**
     * @param array $displayedUser
     * @return array
     */
    private function _getOnlineUser($displayedUser)
    {
        // OnlineUser
        $displayedUser = json_decode($displayedUser);
        $onlineUsers = $this->frontendUserRepository->findByFilter('', [], true, $displayedUser);
        // write online user
        foreach ($onlineUsers as $onlineUser) {
            $output[] = $onlineUser->getUid();
        }
        if (!empty($output)) {
            return $output;
        }
    }
}
