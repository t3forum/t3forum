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

use T3forum\T3forum\Domain\Factory\Forum\PostFactory;
use T3forum\T3forum\Domain\Model\Forum\Attachment;
use T3forum\T3forum\Domain\Model\Forum\Post;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use T3forum\T3forum\Domain\Repository\Forum\AttachmentRepository;
use T3forum\T3forum\Domain\Repository\Forum\ForumRepository;
use T3forum\T3forum\Domain\Repository\Forum\PostRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use T3forum\T3forum\Service\AttachmentService;
use T3forum\T3forum\Utility\Configuration;
use T3forum\T3forum\Utility\Localization;
use T3forum\T3forum\Utility\configuration;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 *
 */
class PostController extends AbstractUserAccessController
{
    /**
     * @var \Mittwald\Typo3Forum\Domain\Repository\Forum\AttachmentRepository
     * @inject
     */
    protected $attachmentRepository;

    /**
     * @var \Mittwald\Typo3Forum\Service\AttachmentService
     * @inject
     */
    protected $attachmentService = null;

    /**
     * @var \Mittwald\Typo3Forum\Domain\Repository\Forum\ForumRepository
     * @inject
     */
    protected $forumRepository;

    /**
     * @var \Mittwald\Typo3Forum\Domain\Repository\Forum\PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * @var \Mittwald\Typo3Forum\Domain\Factory\Forum\PostFactory
     * @inject
     */
    protected $postFactory;

    /**
     * @var \Mittwald\Typo3Forum\Domain\Repository\Forum\TopicRepository
     * @inject
     */
    protected $topicRepository;

    /**
     * @var \Mittwald\Typo3Forum\Utility\configuration
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
     * Listing Action
     *
     * @access public
     * @return void
     */
    public function listAction()
    {
        $showPaginate = false;
        switch ($this->settings['listPosts']) {
            case '2':
                $dataset = $this->postRepository->findByFilter(
                    intval($this->settings['widgets']['newestPosts']['limit']),
                    ['crdate' => 'DESC']
                );
                $partial = 'Post/LatestBox';
                break;
            default:
                $dataset = $this->postRepository->findByFilter();
                $partial = 'Post/List';
                $showPaginate = true;
                break;
        }
        $this->view->assign('showPaginate', $showPaginate);
        $this->view->assign('partial', $partial);
        $this->view->assign('posts', $dataset->toArray());
    }

    /**
     * Add supporter action
     *
     * @access public
     * @param Post $post
     * @return string
     */
    public function addSupporterAction(Post $post)
    {
        /** @var FrontendUser $currentUser */
        $currentUser = $this->authenticationService->getUser();

        // Return if User not logged in or user is post author or user has already supported the post
        if ($currentUser === null
          || $currentUser->isAnonymous()
          || $currentUser === $post->getAuthor()
          || $post->hasBeenSupportedByUser($currentUser)
          || $post->getAuthor()->isAnonymous()
        ) {
            return json_encode(['error' => true, 'error_msg' => 'not_allowed']);
        }

        // Set helpfulCount for Author
        $post->addSupporter($currentUser);
        $this->postRepository->update($post);

        $post->getAuthor()->setHelpfulCount($post->getAuthor()->getHelpfulCount() + 1);
        $post->getAuthor()->increasePoints((int)$this->settings['rankScore']['gotHelpful']);
        $this->frontendUserRepository->update($post->getAuthor());

        $currentUser->increasePoints((int)$this->settings['rankScore']['markHelpful']);
        $this->frontendUserRepository->update($currentUser);

        // output new Data
        return json_encode(
            [
                'error' => false,
                'add' => 0,
                'postHelpfulCount' => $post->getHelpfulCount(),
                'userHelpfulCount' => $post->getAuthor()->getHelpfulCount()
            ]
        );
    }

    /**
     * Remove supporter action
     *
     * @access public
     * @param Post $post
     * @return string
     */
    public function removeSupporterAction(Post $post)
    {
        /** @var FrontendUser $currentUser */
        $currentUser = $this->authenticationService->getUser();

        if (!$post->hasBeenSupportedByUser($currentUser)) {
            return json_encode(['error' => true, 'error_msg' => 'not_allowed']);
        }

        // Set helpfulCount for Author
        $post->removeSupporter($currentUser);
        $this->postRepository->update($post);

        $post->getAuthor()->setHelpfulCount($post->getAuthor()->getHelpfulCount() - 1);
        $post->getAuthor()->decreasePoints((int)$this->settings['rankScore']['gotHelpful']);
        $this->frontendUserRepository->update($post->getAuthor());
        $currentUser->decreasePoints((int)$this->settings['rankScore']['markHelpful']);
        $this->frontendUserRepository->update($currentUser);

        // output new Data
        return json_encode(
            [
                'error' => false,
                'add' => 1,
                'postHelpfulCount' => $post->getHelpfulCount(),
                'userHelpfulCount' => $post->getAuthor()->getHelpfulCount()
            ]
        );
    }

    /**
     * Show action for a single post. The method simply redirects the user to the
     * topic that contains the requested post.
     * This function is called by post summaries (last post link)
     *
     * @access public
     * @param Post $post The post
     * @param Post $quote The Quote
     * @param int $showForm ShowForm
     * @return void
     */
    public function showAction(Post $post, Post $quote = null, $showForm = 0)
    {
        // Assert authentication
        $this->authenticationService->assertReadAuthorization($post);

        $redirectArguments = ['topic' => $post->getTopic(), 'showForm' => $showForm];

        if (!empty($quote)) {
            $redirectArguments['quote'] = $quote;
        }
        $pageNumber = $post->getTopic()->getPageCount();
        if ($pageNumber > 1) {
            $redirectArguments['@widget_0'] = ['currentPage' => $pageNumber];
        }

        // Redirect to the topic->show action.
        $this->redirect('show', 'Topic', null, $redirectArguments);
    }

    /**
     * Displays the form for creating a new post.
     *
     * @dontvalidate $post
     *
     * @access public
     * @param Topic $topic The topic in which the new post is to be created.
     * @param Post $post The new post.
     * @param Post $quote An optional post that will be quoted within the bodytext of the new post.
     * @return void
     */
    public function newAction(Topic $topic, Post $post = null, Post $quote = null)
    {
        // Assert authorization
        $this->authenticationService->assertNewPostAuthorization($topic);

        $posts = $this->postRepository->findForTopic($topic);

        // If no post is specified, create an optionally pre-filled post (if a
        // quoted post was specified).
        if ($post === null) {
            if ($quote !== null) {
                $post = $this->postFactory->createPostWithQuote($quote);
            } else {
                $post = $this->postFactory->createEmptyPost();
            }
        } else {
            $this->authenticationService->assertEditPostAuthorization($post);
        }

        $maxFileUploadSize = $this->configuration->getMaxFileUploadSize();
        $this->view->assignMultiple([
            'maxFileUploadSizeNumeric' => $this->configuration->convertToBytes($maxFileUploadSize),
            'maxFileUploadSize' => $maxFileUploadSize,
            'topic' => $topic,
            'post' => $post,
            'posts' => $posts,
            'currentUser' => $this->frontendUserRepository->findCurrent()
        ]);
    }

    /**
     * Creates a new post.
     *
     * @validate $post \Mittwald\Typo3Forum\Domain\Validator\Forum\PostValidator
     * @validate $attachments \Mittwald\Typo3Forum\Domain\Validator\Forum\AttachmentPlainValidator
     *
     * @access public
     * @param Topic $topic The topic in which the new post is to be created.
     * @param Post $post The new post.
     * @param array $attachments File attachments for the post.
     */
    public function createAction(Topic $topic, Post $post, array $attachments = [])
    {
        // Assert authorization
        $this->authenticationService->assertNewPostAuthorization($topic);

        // Create new post, add the new post to the topic and persist the topic.
        $this->postFactory->assignUserToPost($post);

        if (!empty($attachments)) {
            $attachments = $this->attachmentService->initAttachments($attachments);
            $post->setAttachments($attachments);
        }

        $topic->addPost($post);
        $this->topicRepository->update($topic);

        // All potential listeners (Signal-Slot FTW!)
        $this->signalSlotDispatcher->dispatch(
            Post::class,
            'postCreated',
            ['post' => $post]
        );

        // Display flash message and redirect to topic->show action.
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Post_Create_Success'))
        );
        $this->clearCacheForCurrentPage();

        $redirectArguments = ['topic' => $topic, 'forum' => $topic->getForum()];
        $pageNumber = $topic->getPageCount();
        if ($pageNumber > 1) {
            $redirectArguments['@widget_0'] = ['currentPage' => $pageNumber];
        }
        $this->redirect('show', 'Topic', null, $redirectArguments);
    }

    /**
     * Displays a form for editing a post.
     *
     * @dontvalidate $post
     *
     * @access public
     * @param Post $post The post that is to be edited.
     * @return void
     */
    public function editAction(Post $post)
    {
        if ($post->getAuthor() != $this->authenticationService->getUser()
          || $post->getTopic()->getLastPost()->getAuthor() != $post->getAuthor()
        ) {
            // Assert authorization
            $this->authenticationService->assertModerationAuthorization($post->getTopic()->getForum());
        }

        $maxFileUploadSize = $this->configuration->getMaxFileUploadSize();
        $this->view->assignMultiple([
            'maxFileUploadSizeNumeric' => $this->configuration->convertToBytes($maxFileUploadSize),
            'maxFileUploadSize' => $maxFileUploadSize,
            'post' => $post
        ]);
    }

    /**
     * Updates a post.
     *
     * @access public
     * @param Post $post The post that is to be updated.
     * @param array $attachments File attachments for the post.
     * @return void
     */
    public function updateAction(Post $post, array $attachments = [])
    {
        if ($post->getAuthor() != $this->authenticationService->getUser()
          || $post->getTopic()->getLastPost()->getAuthor() != $post->getAuthor()
        ) {
            // Assert authorization
            $this->authenticationService->assertModerationAuthorization($post->getTopic()->getForum());
        }

        $allAttachments  = $post->getAttachments();

        // Determine attachments to be deleted (if any)
        $attachmentsToDelete = [];
        if ($this->request->hasArgument('deleteAttachment')) {
            $arguments = $this->request->getArguments();
            if (is_array($arguments['deleteAttachment'])) {
                $attachmentsToDelete = $arguments['deleteAttachment'];
            }
        }

        // Delete selected attachments
        if (count($attachmentsToDelete) > 0) {
            $allAttachments->rewind();
            while ($allAttachments->valid()) {
                $storedObject = $allAttachments->current();
                $currentFileName = $storedObject->getFilename();
                if (in_array($currentFileName, $attachmentsToDelete)) {
                    if (file_exists($storedObject->getAbsoluteFilename())) {
                        unlink($storedObject->getAbsoluteFilename());
                    }
                    $allAttachments->detach($storedObject);
                } else {
                    $allAttachments->next();
                }
            }
        }

        if (!empty($attachments)) {
            $attachments = $this->attachmentService->initAttachments($attachments);
            foreach ($attachments as $attachment) {
                $post->addAttachments($attachment);
            }
        }
        $this->postRepository->update($post);

        $this->signalSlotDispatcher->dispatch(
            Post::class,
            'postUpdated',
            ['post' => $post]
        );
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Post_Update_Success'))
        );
        $this->clearCacheForCurrentPage();

        $this->redirect('show', 'Topic', null, ['topic' => $post->getTopic()]);
    }

    /**
     * Displays a confirmation screen in which the user is prompted if a post
     * should really be deleted.
     *
     * @access public
     * @param Post $post The post that is to be deleted.
     * @return void
     */
    public function confirmDeleteAction(Post $post)
    {
        $this->authenticationService->assertDeletePostAuthorization($post);
        $this->view->assign('post', $post);
    }

    /**
     * Deletes a post.
     *
     * @access public
     * @param Post $post The post that is to be deleted.
     * @return void
     */
    public function deleteAction(Post $post)
    {
        // Assert authorization
        $this->authenticationService->assertDeletePostAuthorization($post);

        // Delete the post.
        $postCount = $post->getTopic()->getPostCount();
        $this->postFactory->deletePost($post);
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage(Localization::translate('Post_Delete_Success'))
        );

        // Notify observers and clear cache.
        $this->signalSlotDispatcher->dispatch(
            Post::class,
            'postDeleted',
            ['post' => $post]
        );
        $this->clearCacheForCurrentPage();

        // If there is still on post left in the topic, redirect to the topic
        // view. If we have deleted the last post of a topic (i.e. the topic
        // itself), redirect to the forum view instead.
        if ($postCount > 1) {
            $this->redirect('show', 'Topic', null, ['topic' => $post->getTopic()]);
        } else {
            $this->redirect('show', 'Forum', null, ['forum' => $post->getForum()]);
        }
    }

    /**
     * Displays a preview of a rendered post text.
     *
     * @access public
     * @param string $text The content.
     */
    public function previewAction($text)
    {
        $this->view->assign('text', $text);
    }

    /**
     * Downloads an attachment and increase the download counter
     *
     * @access public
     * @param \Mittwald\Typo3Forum\Domain\Model\Forum\Attachment $attachment
     */
    public function downloadAttachmentAction($attachment)
    {
        $attachment->increaseDownloadCount();
        $this->attachmentRepository->update($attachment);

        //Enforce persistence, since it will not happen regularly because of die() at the end
        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $persistenceManager->persistAll();

        header('Content-type: ' . $attachment->getMimeType());
        header('Content-Type: application/download');
        header('Content-Disposition: attachment; filename="' . $attachment->getFilename() . '"');
        readfile($attachment->getAbsoluteFilename());
        die();
    }
}
