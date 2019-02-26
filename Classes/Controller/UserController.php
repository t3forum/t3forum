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

use T3forum\T3forum\Domain\Exception\Authentication\NotLoggedInException;
use T3forum\T3forum\Domain\Factory\User\PrivateMessageFactory;
use T3forum\T3forum\Domain\Model\Forum\Forum;
use T3forum\T3forum\Domain\Model\Forum\Topic;
use T3forum\T3forum\Domain\Model\SubscribeableInterface;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use T3forum\T3forum\Domain\Model\User\PrivateMessage;
use T3forum\T3forum\Domain\Model\User\PrivateMessageText;
use T3forum\T3forum\Domain\Repository\Forum\ForumRepository;
use T3forum\T3forum\Domain\Repository\Forum\TopicRepository;
use T3forum\T3forum\Domain\Repository\User\NotificationRepository;
use T3forum\T3forum\Domain\Repository\User\PrivateMessageRepository;
use T3forum\T3forum\Domain\Repository\User\RankRepository;
use T3forum\T3forum\Domain\Repository\User\UserfieldRepository;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class UserController extends AbstractUserAccessController
{
    /**
     * @var ForumRepository
     * @inject
     */
    protected $forumRepository = null;

    /**
     * @var PrivateMessageRepository
     * @inject
     */
    protected $privateMessageRepository = null;

    /**
     * @var NotificationRepository
     * @inject
     */
    protected $notificationRepository = null;

    /**
     * @var PrivateMessageFactory
     * @inject
     */
    protected $privateMessageFactory;

    /**
     * @var RankRepository
     * @inject
     */
    protected $rankRepository = null;

    /**
     * @var TopicRepository
     * @inject
     */
    protected $topicRepository = null;

    /**
     * @var UserfieldRepository
     * @inject
     */
    protected $userfieldRepository = null;

    /**
     * Displays a list of all existing users.
     */
    public function indexAction()
    {
        $this->view->assign('users', $this->frontendUserRepository->findForIndex());
    }

    /**
     *  Listing Action.
     */
    public function listAction()
    {
        $showPaginate = false;
        switch ($this->settings['listUsers']) {
            case 'activeUserWidget':
                $dataset['users'] = $this->frontendUserRepository->findByFilter(
                    (int)$this->settings['widgets']['activeUser']['limit'],
                    ['postCountSession' => 'DESC', 'username' => 'ASC']
                );
                $partial = 'User/ActiveBox';
                break;
            case 'helpfulUserWidget':
                $dataset['users'] = $this->frontendUserRepository->findByFilter(
                    (int)$this->settings['widgets']['helpfulUser']['limit'],
                    ['helpfulCountSession' => 'DESC', 'username' => 'ASC']
                );
                $partial = 'User/HelpfulBox';
                break;
            case 'onlineUserWidget':
                //NO DATA - Ajax Reload
                $dataset['count'] = 0;
                $partial = 'User/OnlineBox';
                break;
            case 'rankingList':
                $dataset['ranks'] = $this->rankRepository->findAllForRankingOverview();
                $partial = 'User/ListRanking';
                break;
            case 'topUserList':
                $dataset['users'] = $this->frontendUserRepository->findTopUserByPoints(50);
                $partial = 'User/ListTopUser';
                break;
            default:
                $dataset['users'] = $this->frontendUserRepository->findByFilter(0, ['username' => 'ASC']);
                $partial = 'User/List';
                break;
        }

        $this->view->assign('showPaginate', $showPaginate);
        $this->view->assign('partial', $partial);
        $this->view->assign('dataset', $dataset);
    }

    /**
     * Lists all posts of a specific user. If no user is specified, this action lists all
     * posts of the current user.
     *
     * @param FrontendUser $user
     * @throws NotLoggedInException
     */
    public function listPostsAction(FrontendUser $user = null)
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your own posts.', 1288084981);
        }
        $this->view
            ->assign('topics', $this->topicRepository->findByPostAuthor($user))
            ->assign('user', $user);
    }

    /**
     * Lists all topics of a specific user. If no user is specified, this action lists all
     * topics of the current user.
     *
     * @param FrontendUser $user
     * @throws NotLoggedInException
     */
    public function listFavoritesAction(FrontendUser $user = null)
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your own posts.', 1288084981);
        }
        $this->view
            ->assign('topics', $this->topicRepository->findTopicsFavSubscribedByUser($user))
            ->assign('user', $user);
    }

    /**
     * Lists all topics of a specific user. If no user is specified, this action lists all
     * topics of the current user.
     *
     * @param FrontendUser $user
     * @throws NotLoggedInException
     */
    public function listTopicsAction(FrontendUser $user = null)
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your own posts.', 1288084981);
        }
        $this->view
            ->assign('topics', $this->topicRepository->findTopicsCreatedByAuthor($user))
            ->assign('user', $user);
    }

    /**
     * Lists all questions of a specific user. If no user is specified, this action lists all
     * posts of the current user.
     *
     * @param FrontendUser $user
     * @throws NotLoggedInException
     */
    public function listQuestionsAction(FrontendUser $user = null)
    {
        if ($user === null) {
            $user = $this->getCurrentUser();
        }
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your own posts.', 1288084981);
        }
        $this->view
            ->assign('topics', $this->topicRepository->findQuestions(null, true, $user))
            ->assign('user', $user);
    }

    /**
     * Lists all messages of a specific user. If no user is specified, this action lists all
     * messages of the current user.
     *
     * @param FrontendUser $opponent The dialog with which user should be shown. If null get first dialog.
     * @throws NotLoggedInException
     */
    public function listMessagesAction(FrontendUser $opponent = null)
    {
        $user = $this->getCurrentUser();
        if (!$user instanceof FrontendUser || $user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your own posts.', 1288084981);
        }
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $dialog */
        $dialog = null;
        $partner = 'unknown';
        $userList = $this->privateMessageRepository->findStartedConversations($user);

        if (!empty($userList)) {
            if ($opponent === null) {
                $dialog = $this->privateMessageRepository->findMessagesBetweenUser(
                    $userList[0]->getFeuser(),
                    $userList[0]->getOpponent()
                );
                $partner = $userList[0]->getOpponent();
            } else {
                $dialog = $this->privateMessageRepository->findMessagesBetweenUser($user, $opponent);
                $partner = $opponent;
            }

            foreach ($dialog as $pm) {
                if ($pm->getOpponent()->getUid() == $user->getUid()) {
                    if ($pm->getUserRead() == 1) {
                        break;
                    } // if user already read this message, the next should be already read
                    $pm->setUserRead(1);
                    $this->privateMessageRepository->update($pm);
                }
            }
        }
        $this->view->assignMultiple([
            'userList' => $userList,
            'dialog' => $dialog,
            'currentUser' => $user,
            'partner' => $partner,
        ]);
    }

    /**
     * Shows the form for creating a new message
     *
     * @param FrontendUser $recipient
     * @throws NotLoggedInException
     * @return void
     */
    public function newMessageAction(FrontendUser $recipient = null)
    {
        $user = $this->getCurrentUser();
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in.', 1288084981);
        }
        $readonly = 0;
        if ($recipient !== null) {
            $recipient = $recipient->getUsername();
            $readonly = 1;
        }
        $this->view->assign('user', $user)->assign('recipient', $recipient)->assign('readonly', $readonly);
    }

    /**
     * Create a new message
     *
     * @param string $recipient
     * @param string $text
     * @throws NotLoggedInException
     * @validate $recipient \T3forum\T3forum\Domain\Validator\User\PrivateMessageRecipientValidator
     */
    public function createMessageAction($recipient, $text)
    {
        $user = $this->getCurrentUser();
        $recipient = $this->frontendUserRepository->findOneByUsername($recipient);
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in.', 1288084981);
        }
        /** @var PrivateMessageText $message */
        $message = $this->objectManager->get(PrivateMessageText::class);
        $message->setMessageText($text);
        $pmFeUser = $this->privateMessageFactory->createPrivateMessage(
            $user,
            $recipient,
            $message,
            PrivateMessage::TYPE_SENDER,
            1
        );
        $pmRecipient = $this->privateMessageFactory->createPrivateMessage(
            $recipient,
            $user,
            $message,
            PrivateMessage::TYPE_RECIPIENT,
            0
        );
        $this->privateMessageRepository->add($pmFeUser);
        $this->privateMessageRepository->add($pmRecipient);
        $this->redirect('listMessages');
    }

    /**
     * Lists all messages of a specific user. If no user is specified, this action lists all
     * messages of the current user.
     *
     * @throws NotLoggedInException
     */
    public function listNotificationsAction()
    {
        /** @var FrontendUser $user */
        $user = $this->authenticationService->getUser();
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in.', 1288084981);
        }
        $notifications = $this->notificationRepository->findNotificationsForUser($user);

        foreach ($notifications as $notification) {
            if ($notification->getUserRead() == 1) {
                break;
            } // if user already read this notification, the next should be already read
            $notification->setUserRead(1);
            $this->notificationRepository->update($notification);
        }

        $this->view->assignMultiple([
            'notifications' => $notifications,
            'currentUser' => $user,
        ]);
    }

    /**
     * disableUserAction
     *
     * @param FrontendUser $user
     * @throws NotLoggedInException
     * @throws IllegalObjectTypeException
     * @return void
     */
    public function disableUserAction(FrontendUser $user = null)
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in.', 1288084981);
        }
        $allowed = false;
        foreach ($currentUser->getUsergroup() as $group) {
            if ($group->getUserMod()) {
                $allowed = true;
            }
        }
        if (!$allowed) {
            throw new NotLoggedInException('You need to be logged in as Admin.', 1288344981);
        }

        $user->setDisable(true);
        $this->frontendUserRepository->update($user);
        $this->redirect('show', 'User', 't3forum', ['user' => $user]);
    }

    /**
     * Displays a single user.
     *
     * @param FrontendUser $user The user whose profile is to be displayed.
     */
    public function showAction(FrontendUser $user = null)
    {
        if ($user === null) {
            $this->redirect('show', null, null, ['user' => $this->getCurrentUser()]);
        }
        $lastFiveTopics = $this->topicRepository
            ->findByPostAuthor($user)
            ->getQuery()
            ->setLimit(5)
            ->execute();
        $this->view->assignMultiple([
            'user' => $user,
            'currentUser' => $this->getCurrentUser(),
            'userfields' => $this->userfieldRepository->findAll(),
            'topics' => $lastFiveTopics,
            'questions' => $this->topicRepository->findQuestions(6, false, $user),
            'myTopics' => $this->topicRepository->findTopicsCreatedByAuthor($user, 6),
        ]);
    }

    /**
     * Subscribes the current user to a forum or a topic.
     *
     * @param Forum $forum The forum that is to be subscribed. Either this value or parameter $topic must be != NULL.
     * @param Topic $topic The topic that is to be subscribed. Either this value or parameter $forum must be != NULL.
     * @param bool $unsubscribe TRUE to unsubscribe the forum or topic instead.
     * @throws IllegalObjectTypeException
     * @throws NotLoggedInException
     * @throws InvalidArgumentValueException
     * @return void
     */
    public function subscribeAction(Forum $forum = null, Topic $topic = null, $unsubscribe = false)
    {
        // Validate arguments
        if ($forum === null && $topic === null) {
            throw new InvalidArgumentValueException(
                'You need to subscribe a Forum or Topic!',
                1285059341
            );
        }
        $user = $this->getCurrentUser();
        if (!is_object($user) || $user->isAnonymous()) {
            throw new NotLoggedInException(
                'You need to be logged in to subscribe or unsubscribe an object.',
                1335121482
            );
        }

        // Create subscription
        $object = $topic ? $topic : $forum;

        if ($unsubscribe) {
            $user->removeSubscription($object);
        } else {
            $user->addSubscription($object);
        }

        // Update user and redirect to subscription object.
        $this->frontendUserRepository->update($user);
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage($this->getSubscriptionFlashMessage($object, $unsubscribe))
        );
        $this->clearCacheForCurrentPage();
        $this->redirectToSubscriptionObject($object);
    }

    /**
     * Fav Subscribes the current user to a forum or a topic.
     *
     * @param Forum $forum The forum that is to be subscribed. Either this value or parameter $topic must be != NULL.
     * @param Topic $topic The topic that is to be subscribed. Either this value or parameter $forum must be != NULL.
     * @param bool $unsubscribe TRUE to unsubscribe the forum or topic instead.
     * @throws InvalidArgumentValueException
     * @throws NotLoggedInException
     * @return void
     */
    public function favSubscribeAction(Forum $forum = null, Topic $topic = null, $unsubscribe = false)
    {
        // Validate arguments
        if ($forum === null && $topic === null) {
            throw new InvalidArgumentValueException(
                'You need to subscribe a Forum or Topic!',
                1285059341
            );
        }
        $user = $this->getCurrentUser();
        if ($user->isAnonymous()) {
            throw new NotLoggedInException(
                'You need to be logged in to subscribe or unsubscribe an object.',
                1335121482
            );
        }

        // Create subscription
        $object = $forum ? $forum : $topic;

        if ($unsubscribe) {
            $user->removeFavSubscription($object);
            $topic->getAuthor()->decreasePoints((int)$this->settings['rankScore']['gotFavorite']);
        } else {
            $user->addFavSubscription($object);
            $topic->getAuthor()->increasePoints((int)$this->settings['rankScore']['gotFavorite']);
        }

        // Update user and redirect to subscription object.
        $this->frontendUserRepository->update($user);
        $this->frontendUserRepository->update($topic->getAuthor());
        $this->controllerContext->getFlashMessageQueue()->enqueue(
            new FlashMessage($this->getSubscriptionFlashMessage($object, $unsubscribe))
        );
        $this->clearCacheForCurrentPage();
        $this->redirectToSubscriptionObject($object);
    }

    /**
     * Displays all topics and forums subscribed by the current user.
     *
     * @throws NotLoggedInException
     */
    public function listSubscriptionsAction()
    {
        $user = $this->getCurrentUser();
        if ($user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your own subscriptions!', 1335120249);
        }

        $this->view->assignMultiple([
            'forums' => $this->forumRepository->findBySubscriber($user),
            'topics' => $this->topicRepository->findBySubscriber($user),
            'user' => $user,
        ]);
    }

    /**
     * Displays a dashboard for the current user
     *
     * @throws \T3forum\T3forum\Domain\Exception\Authentication\NotLoggedInException
     * @return void
     */
    public function dashboardAction()
    {
        $user = $this->getCurrentUser();
        if (!is_object($user) || $user->isAnonymous()) {
            throw new NotLoggedInException('You need to be logged in to view your dashboard!', 1335120249);
        }
        $this->view->assignMultiple([
            'user' => $user,
            'myNotifications' => $this->notificationRepository->findNotificationsForUser($user, 6),
            'myMessages' => $this->privateMessageRepository->findReceivedMessagesForUser($user, 6),
            'myFavorites' => $this->topicRepository->findTopicsFavSubscribedByUser($user, 6),
            'myTopics' => $this->topicRepository->findTopicsCreatedByAuthor($user, 6),
        ]);
    }

    /**
     * @TODO: Is this empty to avoid usage in upper class?
     *
     * @param string $searchValue
     * @param string $filter
     * @param int $order
     * @return void
     */
    public function searchUserAction($searchValue = null, $filter = null, $order = null)
    {
    }

    /**
     * Redirects the user to the display view of a subscribeable object. This may
     * either be a forum or a topic, so this method redirects either to the
     * Forum->show or the Topic->show action.
     *
     * @param SubscribeableInterface $object A subscribeable object, i.e. either a forum or a topic.
     */
    protected function redirectToSubscriptionObject(SubscribeableInterface $object)
    {
        if ($object instanceof Forum) {
            $this->redirect('show', 'Forum', null, ['forum' => $object]);
        }
        if ($object instanceof Topic) {
            $this->redirect('show', 'Topic', null, ['topic' => $object, 'forum' => $object->getForum()]);
        }
    }

    /**
     * Generates a flash message for when a subscription has successfully been
     * created or removed.
     *
     * @TODO: delimiter depends on version,
     *        probably '_' is not used anymore at all (was for versions 4 + 6 ...), remove condition and '_' then
     *
     * @param SubscribeableInterface $object
     * @param bool $unsubscribe
     * @return string A flash message.
     */
    protected function getSubscriptionFlashMessage(SubscribeableInterface $object, $unsubscribe = false)
    {
        $class = get_class($object);
        if (strpos($class, '\\')) {
            $delimiter = '\\';
        } elseif (strpos($class, '_')) {
            $delimiter = '_';
        }
        $type = array_pop(explode($delimiter, $class));
        $userAction = ($unsubscribe ? 'Unsubscribe' : 'Subscribe');
        $key = 'User_' . $userAction . '_' . $type . '_Success';
        $subscriptionFlashMessage = LocalizationUtility::translate($key, 'T3forum', [$object->getTitle()]);
        return $subscriptionFlashMessage;
    }
}
