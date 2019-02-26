<?php
namespace T3forum\T3forum\Domain\Model\Forum;

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

use T3forum\T3forum\Domain\Model\AccessibleInterface;
use T3forum\T3forum\Domain\Model\NotifiableInterface;
use T3forum\T3forum\Domain\Model\User\AnonymousFrontendUser;
use T3forum\T3forum\Domain\Model\User\FrontendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * A forum post. Forum posts are submitted to the access control mechanism and can be
 * subscribed by users.
 */
class Post extends AbstractEntity implements AccessibleInterface, NotifiableInterface
{
    /**
     * The post text.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $text;

    /**
     * The rendered post text (contains raw HTML). This attribute has been
     * implemented for performance reasons. When being rendered (i.e. bb
     * codes being replaced with corresponding HTML codes, etc.), the
     * rendered output is cached in this property.
     *
     * @var string
     */
    protected $renderedText;

    /**
     * The post author.
     *
     * @var \T3forum\T3forum\Domain\Model\User\FrontendUser
     */
    protected $author;

    /**
     * The author's username. Necessary for anonymous postings.
     * @var string
     */
    protected $authorName = '';

    /**
     * The topic.
     * @var \T3forum\T3forum\Domain\Model\Forum\Topic
     */
    protected $topic;

    /**
     * Creation date.
     * @var \DateTime
     */
    protected $crdate;

    /**
     * All subscribers of this forum.
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\T3forum\T3forum\Domain\Model\User\FrontendUser>
     * @lazy
     */
    protected $supporters;

    /**
     * Attachments.
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\T3forum\T3forum\Domain\Model\Forum\Attachment>
     * @lazy
     */
    protected $attachments;

    /**
     * @var string
     */
    protected $deleteAttachment;

    /**
     * helpfull count
     * @var int
     */
    protected $helpfulCount;

    /**
     * Creates a new post.
     *
     * @param string $text The post text.
     */
    public function __construct($text = '')
    {
        $this->attachments = new ObjectStorage();
        $this->supporters = new ObjectStorage();
        $this->crdate = new \DateTime();
        $this->text = $text;
    }

    public function toArray()
    {
        return [
            'text' => $this->getText(),
            'renderedText' => $this->renderedText,
            'author' => $this->getAuthor(),
            'authorName' => $this->getAuthorName(),
            'topic' => $this->getTopic(),
            'crdate' => $this->getCrdate(),
            'supporters' => $this->getSupporters(),
            'attachments' => $this->getAttachments(),
            'helpfulCount' => $this->getHelpfulCount(),
            'uid' => $this->getUid(),
            '_localizedUid' => $this->_localizedUid,
            '_languageUid' => $this->_languageUid,
            '_versionedUid' => $this->_versionedUid,
            'pid' => $this->getPid(),
            'deleteAttachment' => $this->getDeleteAttachment(),
        ];
    }

    /**
     * Gets all users who have subscribed to this forum.
     *
     * @return ObjectStorage<\T3forum\T3forum\Domain\Model\User\FrontendUser> All subscribers of this forum.
     */
    public function getSupporters()
    {
        return $this->supporters;
    }

    /**
     * Gets the helpful count of this post.
     * @return int The helpful count.
     */
    public function getHelpfulCount()
    {
        return $this->helpfulCount;
    }

    /**
     * Gets the text.
     * @return string The text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Gets the post name. This is just an alias for the topic->getTitle method.
     * @return string The post name.
     */
    public function getName()
    {
        return $this->topic->getTitle();
    }

    /**
     * Alias for getText(). Necessary to implement the NotifiableInterface.
     * @return string The post text.
     */
    public function getDescription()
    {
        return $this->getText();
    }

    /**
     * Gets the post author.
     * @return FrontendUser author
     */
    public function getAuthor()
    {
        if ($this->author instanceof LazyLoadingProxy) {
            $this->author->_loadRealInstance();
        }
        if ($this->author === null) {
            $this->author = new AnonymousFrontendUser();
            if ($this->authorName) {
                $this->author->setUsername($this->authorName);
            }
        }

        return $this->author;
    }

    /**
     * Gets the post author's name. Diffentiates between posts created by logged in
     * users (in this case this user's username is returned) and posts by anonymous
     * users.
     * @return string The author's username.
     */
    public function getAuthorName()
    {
        if ($this->getAuthor()->isAnonymous()) {
            return $this->authorName;
        } else {
            return $this->getAuthor()->getUsername();
        }
    }

    /**
     * Gets the topic.
     * @return Topic A topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Gets the forum.
     * @return Forum
     */
    public function getForum()
    {
        return $this->topic->getForum();
    }

    /**
     * Gets the post's timestamp.
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->crdate;
    }

    /**
     * Gets the post's crdate.
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Gets the post's attachments.
     * @return ObjectStorage<\T3forum\T3forum\Domain\Model\Forum\Attachment>
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Overrides the isPropertyDirty method. See http://forge.typo3.org/issues/8952
     * for further information.
     *
     * @param mixed $previousValue
     * @param mixed $currentValue
     *
     * @return bool
     */
    protected function isPropertyDirty($previousValue, $currentValue)
    {
        if ($currentValue instanceof Forum || $currentValue instanceof Topic) {
            return false;
        } else {
            return parent::isPropertyDirty($previousValue, $currentValue);
        }
    }

    /**
     * Performs an access check for this post.
     *
     * @access private
     *
     * @param FrontendUser $user
     * @param string $accessType
     * @return bool
     */
    public function checkAccess(FrontendUser $user = null, $accessType = Access::TYPE_READ)
    {
        switch ($accessType) {
            case Access::TYPE_EDIT_POST:
            case Access::TYPE_DELETE_POST:
                $access = $this->checkEditOrDeletePostAccess($user, $accessType);
                break;
            default:
                $access = $this->topic->checkAccess($user, $accessType);
                break;
        }
        return $access;
    }

    /**
     * Determines if a user may edit this post. This is only possible if EITHER:
     * a1.) The user is the author of this post, AND
     * a2.) This post is the last post in the topic, AND
     * a3.) The topic generally permits posts to be edited (this would not be the
     *      case if the topic would e.g. be closed).
     * OR:
     * b.)  The current user has moderator access to the forum.
     *
     * @param FrontendUser $user The user for which the authenication is to be checked.
     * @param string $operation
     * @return bool TRUE, if the user is allowed to edit this post, otherwise FALSE.
     */
    public function checkEditOrDeletePostAccess(FrontendUser $user = null, $operation)
    {
        if ($user === null || $user->isAnonymous()) {
            return false;
        } else {
            if ($this->getForum()->checkModerationAccess($user)) {
                return true;
            }

            $currentUserIsAuthor = ($user === $this->getAuthor());
            $postIsLastPostInTopic = ($this === $this->getTopic()->getLastPost());
            $topicGrantsAccess = $this->getTopic()->checkAccess($user, $operation);

            if ($currentUserIsAuthor && $postIsLastPostInTopic && $topicGrantsAccess) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the city value
     *
     * @param $count
     *
     * @return void
     * @api
     */
    public function setHelpfulCount($count)
    {
        $this->helpfulCount = $count;
    }

    /**
     * Sets the post author.
     *
     * @param FrontendUser $author The post author.
     *
     * @return void
     */
    public function setAuthor(FrontendUser $author)
    {
        if ($author->isAnonymous()) {
            $this->author = null;
        } else {
            $this->author = $author;
        }
    }

    /**
     * Sets the post author's name. Necessary for anonymous postings.
     *
     * @param $authorName string The author's name.
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
    }

    /**
     * Sets the post text.
     *
     * @param string $text The post text.
     *
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
        // Reset the rendered text. It will be filled again when the post
        // is rendered.
        $this->renderedText = '';
    }

    /**
     * Sets the attachments.
     *
     * @param ObjectStorage $attachments The attachments.
     * @validate $attachments Tx_T3forum_Domain_Validator_Forum_AttachmentValidator
     *
     * @return void
     */
    public function setAttachments(ObjectStorage $attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * Adds an or more attachments.
     *
     * @param Attachment $attachments The attachment.
     * @return void
     */
    public function addAttachments(Attachment $attachments)
    {
        /* @var Attachment */
        $this->attachments->attach($attachments);
    }

    /**
     * Removes an attachment.
     *
     * @param Attachment $attachment The attachment.
     * @return void
     */
    public function removeAttachment(Attachment $attachment)
    {
        if (file_exists($attachment->getAbsoluteFilename())) {
            unlink($attachment->getAbsoluteFilename());
        }
        $this->attachments->detach($attachment);
    }

    /**
     * Determines whether this topic has been read by a certain user.
     *
     * @param FrontendUser $supporter The user who is to be checked.
     * @return bool TRUE, if the user did read this topic, otherwise FALSE.
     */
    public function hasBeenSupportedByUser(FrontendUser $supporter = null)
    {
        return $supporter ? $this->supporters->contains($supporter) : true;
    }

    /**
     * @param Topic $topic
     * @return void
     */
    public function setTopic(Topic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * Marks this topic as read by a certain user.
     *
     * @param FrontendUser $supporter The user who read this topic.
     * @return void
     */
    public function addSupporter(FrontendUser $supporter)
    {
        $this->setHelpfulCount($this->getHelpfulCount() + 1);
        $this->supporters->attach($supporter);
    }

    /**
     * Mark this topic as unread for a certain user.
     *
     * @param FrontendUser $supporter The user for whom to mark this topic as unread.
     *
     * @return void
     */
    public function removeSupporter(FrontendUser $supporter)
    {
        $this->setHelpfulCount($this->getHelpfulCount() - 1);
        $this->supporters->detach($supporter);
    }

    /**
     * Mark this topic as unread for all users.
     * @return void
     */
    public function removeAllSupporters()
    {
        $this->supporters = new ObjectStorage();
    }

    /**
     * Gets all attachments which should be deleted
     *
     * @return array;
     */
    public function getDeleteAttachment()
    {
        return $this->deleteAttachment;
    }
}
