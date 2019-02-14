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

use T3forum\T3forum\Domain\Exception\Authentication\NoAccessException;
use T3forum\T3forum\Domain\Factory\Forum\PostFactory;
use T3forum\T3forum\Domain\Factory\Forum\TopicFactory;
use T3forum\T3forum\Domain\Model\Forum\Forum;
use T3forum\T3forum\Domain\Model\Forum\Post;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Domain\Repository\Forum\AdRepository;
use T3forum\T3forum\Domain\Repository\Forum\CriteriaRepository;
use T3forum\T3forum\Domain\Repository\Forum\ForumRepository;
use T3forum\T3forum\Domain\Repository\Forum\PostRepository;
use T3forum\T3forum\Domain\Repository\Forum\TagRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use T3forum\T3forum\Service\AttachmentService;
use T3forum\T3forum\Service\SessionHandlingService;
use T3forum\T3forum\Service\TagService;
use T3forum\T3forum\Utility\Configuration;
use T3forum\T3forum\Utility\configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 */
class TopicController extends AbstractUserAccessController
{
    /**
     * @var AdRepository
     * @inject
     */
    protected $adRepository;

    /**
     * @var AttachmentService
     * @inject
     */
    protected $attachmentService;

    /**
     * @var CriteriaRepository
     * @inject
     */
    protected $criteraRepository;

    /**
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository;

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
     * @var SessionHandlingService
     * @inject
     */
    protected $sessionHandling;

    /**
     * @var TagRepository
     * @inject
     */
    protected $tagRepository;

    /**
     * @var TagService
     * @inject
     */
    protected $tagService = null;

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
     * @var configuration
     */
    private $configuration;

    /**
     * Initialize object
     *
     * @access public
     */
    public function initializeObject()
    {
        $this->configuration = GeneralUtility::makeInstance(Configuration::class);
    }

    /**
     * Listing Action.
     *
     * @access public
     * @return void
     */
    public function listAction()
    {
        $showPaginate = false;
        switch ($this->settings['listTopics']) {
            case '2':
                $dataset = $this->topicRepository->findQuestions();
                $showPaginate = true;
                $partial = 'Topic/List';
                break;
            case '3':
                $dataset = $this->topicRepository->findQuestions(
                    intval($this->settings['maxTopicItems'])
                );
                $partial = 'Topic/QuestionBox';
                break;
            case '4':
                $dataset = $this->topicRepository->findPopularTopics(
                    intval($this->settings['popularTopicTimeDiff']),
                    intval($this->settings['maxTopicItems'])
                );
                $partial = 'Topic/ListBox';
                break;
            default:
                $dataset = $this->topicRepository->findAll();
                $partial = 'Topic/List';
                $showPaginate = true;
                break;
        }
        $this->view->assign('showPaginate', $showPaginate);
        $this->view->assign('partial', $partial);
        $this->view->assign('topics', $dataset);
    }

    /**
     * List latest action.
     *
     * @access public
     */
    public function listLatestAction()
    {
        if (!empty($this->settings['countLatestPost'])) {
            $limit = (int)$this->settings['countLatestPost'];
        } else {
            $limit = 3;
        }

        $topics = $this->topicRepository->findLatest(0, $limit);
        $this->view->assign('topics', $topics);
    }

    /**
     * Show action. Displays a single topic and all posts contained in this topic.
     *
     * @access public
     * @param Topic $topic The topic that is to be displayed.
     * @param Post $quote An optional post that will be quoted within the bodytext of the new post.
     * @param int $showForm ShowForm
     */
    public function showAction(Topic $topic, Post $quote = null, $showForm = 0)
    {
        $posts = $this->postRepository->findForTopic($topic);

        if ($quote != false) {
            $this->view->assign('quote', $this->postFactory->createPostWithQuote($quote));
        }
        // Set Title
        $GLOBALS['TSFE']->page['title'] = $topic->getTitle();

        $googlePlus = $topic->getAuthor()->getGoogle();
        if ($googlePlus) {
            $this->response->addAdditionalHeaderData('<link rel="author" href="' . $googlePlus . '"/>');
        }

        // send signal for simple read count
        $this->signalSlotDispatcher->dispatch(
            Topic::class,
            'topicDisplayed',
            ['topic' => $topic]
        );

        $this->authenticationService->assertReadAuthorization($topic);
        $this->markTopicRead($topic);

        $this->view->assignMultiple([
            'posts' => $posts,
            'showForm' => $showForm,
            'topic' => $topic,
            'user' => $this->authenticationService->getUser(),
        ]);
    }

    /**
     * New action. Displays a form for creating a new topic.
     *
     * @param Forum $forum The forum in which the new topic is to be created.
     * @param Post $post The first post of the new topic.
     * @param string $subject The subject of the new topic
     *
     * @dontvalidate $post
     */
    public function newAction(Forum $forum, Post $post = null, $subject = null)
    {
        $maxFileUploadSize = $this->configuration->getMaxFileUploadSize();
        $this->authenticationService->assertNewTopicAuthorization($forum);
        $this->view->assignMultiple([
            'maxFileUploadSizeNumeric' => $this->configuration->convertToBytes($maxFileUploadSize),
            'maxFileUploadSize' => $maxFileUploadSize,
            'criteria' => $forum->getCriteria(),
            'currentUser' => $this->frontendUserRepository->findCurrent(),
            'forum' => $forum,
            'post' => $post,
            'subject' => $subject,
        ]);
    }

    /**
     * Creates a new topic.
     *
     * @validate $post \T3forum\T3forum\Domain\Validator\Forum\PostValidator
     * @validate $attachments \T3forum\T3forum\Domain\Validator\Forum\AttachmentPlainValidator
     * @validate $subject NotEmpty
     *
     * @access public
     * @param Forum $forum The forum in which the new topic is to be created.
     * @param Post $post The first post of the new topic.
     * @param string $subject The subject of the new topic
     * @param array $attachments File attachments for the post.
     * @param string $question The flag if the new topic is declared as question
     * @param array $criteria All submitted criteria with option.
     * @param string $tags All defined tags for this topic
     * @param string $subscribe The flag if the new topic is subscribed by author
     */
    public function createAction(
        Forum $forum,
        Post $post,
        $subject,
        $attachments = [],
        $question = '',
        $criteria = [],
        $tags = '',
        $subscribe = ''
    ) {
        // Assert authorization
        $this->authenticationService->assertNewTopicAuthorization($forum);

        // Create the new post; add the new post to a new topic and add the new
        // topic to the forum. Then persist the forum object. Not as complicated
        // as is sounds, honestly!
        $this->postFactory->assignUserToPost($post);

        if (!empty($attachments)) {
            $attachments = $this->attachmentService->initAttachments($attachments);
            $post->setAttachments($attachments);
        }

        if ($tags) {
            $tags = $this->tagService->initTags($tags);
            foreach ($tags as $tag) {
                if ($tag->getUid === null) {
                    $this->tagRepository->add($tag);
                }
            }
        } else {
            $tags = null;
        }

        $topic = $this->topicFactory->createTopic(
            $forum,
            $post,
            $subject,
            intval($question),
            $criteria,
            $tags,
            intval($subscribe)
        );

        // Notify potential listeners.
        $this->signalSlotDispatcher->dispatch(
            Topic::class,
            'topicCreated',
            ['topic' => $topic]
        );
        $this->clearCacheForCurrentPage();
        $uriBuilder = $this->controllerContext->getUriBuilder();
        $uri = $uriBuilder->setTargetPageUid($this->settings['pids']['Forum'])
            ->setArguments([
                'tx_t3forum_pi1[forum]' => $forum->getUid(),
                'tx_t3forum_pi1[controller]' => 'Forum',
                'tx_t3forum_pi1[action]' => 'show'
            ])->build();
        $this->purgeUrl('http://' . $_SERVER['HTTP_HOST'] . '/' . $uri);

        // Redirect to single forum display view
        $this->redirect(
            'show',
            'Forum',
            null,
            ['forum' => $forum]
        );
    }

    /**
     * Sets a post as solution
     *
     * @access public
     * @param Post $post The post to be marked as solution.
     * @throws NoAccessException
     */
    public function solutionAction(Post $post)
    {
        if (!$post->getTopic()->checkSolutionAccess($this->authenticationService->getUser())) {
            throw new NoAccessException('Not allowed to set solution by current user.');
        }
        $this->topicFactory->setPostAsSolution($post->getTopic(), $post);

        $this->redirect(
            'show',
            'Topic',
            null,
            ['topic' => $post->getTopic()]
        );
    }

    /**
     * Marks a topic as read by the current user.
     *
     * @access public
     * @param Topic $topic The topic that is to be marked as read.
     */
    protected function markTopicRead(Topic $topic)
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser === null || $currentUser->isAnonymous()) {
            return;
        } else {
            if ((false === $topic->hasBeenReadByUser($currentUser))) {
                $currentUser->addReadObject($topic);
                $this->frontendUserRepository->update($currentUser);
            }
        }
    }
}
