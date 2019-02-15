<?php
namespace T3forum\T3forum\Service\Notification;

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
use T3forum\T3forum\Domain\Model\NotifiableInterface;
use T3forum\T3forum\Domain\Model\SubscribeableInterface;
use T3forum\T3forum\Service\AbstractService;
use T3forum\T3forum\Service\Mailing\HTMLMailingService;
use T3forum\T3forum\Utility\Localization;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Service class for notifications. This service notifies subscribers of
 * forums and topic about new posts within the subscribed objects.
 */
class NotificationService extends AbstractService implements NotificationServiceInterface
{
    /**
     * @var HTMLMailingService
     * @inject
     */
    protected $htmlMailingService;

    /**
     * @var UriBuilder
     * @inject
     */
    protected $uriBuilder;

    /**
     * @var ConfigurationBuilder
     * @inject
     */
    protected $configurationBuilder;

    /**
     * Entire TypoScript t3forum settings
     *
     * @var array
     */
    protected $settings;

    public function initializeObject()
    {
        $this->settings = $this->configurationBuilder->getSettings();
    }

    /**
     * Notifies subscribers of a subscribeable objects about a new notifiable object
     * within the subscribeable object, e.g. of a new post within a subscribed topic.
     *
     * 2nd argument ($notificationObject) may for example be a new post within an
     * observed topic or forum or a new topic within an observed forum.
     *
     * @param SubscribeableInterface $subscriptionObject Subscribed object. This may for example be a forum or a topic.
     * @param NotifiableInterface $notificationObject Object that the subscriber is notified about.
     * @return void
     *
     */
    public function notifySubscribers(
        SubscribeableInterface $subscriptionObject,
        NotifiableInterface $notificationObject
    ) {
        $topic = $subscriptionObject;
        $post  = $notificationObject;

        $subject = Localization::translate('Mail_Subscribe_NewPost_Subject');
        $messageTemplate = Localization::translate('Mail_Subscribe_NewPost_Body');
        $postAuthor = $post->getAuthor()->getUsername();
        $arguments = [
            'tx_t3forum_pi1[controller]' => 'Topic',
            'tx_t3forum_pi1[action]' => 'show',
            'tx_t3forum_pi1[topic]' => $topic->getUid()
        ];
        $pageNumber = $post->getTopic()->getPageCount();
        if ($pageNumber > 1) {
            $arguments['@widget_0']['currentPage'] = $pageNumber;
        }

        $topicLink = $this->uriBuilder
            ->setTargetPageUid($this->settings['pids']['Forum'])
            ->setArguments($arguments)
            ->setCreateAbsoluteUri(true)
            ->build();
        $topicLink = '<a href="' . $topicLink . '">' . $topic->getTitle() . '</a>';
        $this->uriBuilder->reset();
        $unSubscribeLink = $this->uriBuilder
            ->setTargetPageUid($this->settings['pids']['Forum'])
            ->setArguments([
                'tx_t3forum_pi1[topic]' => $topic->getUid(),
                'tx_t3forum_pi1[controller]' => 'User',
                'tx_t3forum_pi1[action]' => 'subscribe',
                'tx_t3forum_pi1[unsubscribe]' => 1,
            ])
            ->setCreateAbsoluteUri(true)
            ->build();

        $unSubscribeLink = '<a href="' . $unSubscribeLink . '">' . $unSubscribeLink . '</a>';
        foreach ($topic->getSubscribers() as $subscriber) {
            if ($subscriber->getUid() != $post->getAuthor()->getUid()) {
                $marker = [
                    '###RECIPIENT###' => $subscriber->getUsername(),
                    '###POST_AUTHOR###' => $postAuthor,
                    '###TOPIC_LINK###' => $topicLink,
                    '###UNSUBSCRIBE_LINK###' => $unSubscribeLink,
                    '###FORUM_NAME###' => $this->settings['mailing']['sender']['name']
                ];
                $message = $messageTemplate;
                foreach ($marker as $name => $value) {
                    $message = str_replace($name, $value, $message);
                }
                $this->htmlMailingService->sendMail($subscriber, $subject, nl2br($message));
            }
        }
    }
}
