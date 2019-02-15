<?php
namespace T3forum\T3forum\Service;

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

use T3forum\T3forum\Domain\Model\Forum\Attachment;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AttachmentService implements SingletonInterface
{
    /**
     * Instance of the Extbase object manager.
     *
     * @access protected
     * @var ObjectManagerInterface
     * @inject
     */
    protected $objectManager = null;

    /**
     * Converts array of sent $_FILES to an ObjectStorage wizth object(s) of type
     * \T3forum\T3forum\Domain\Model\Forum\Attachment and moves the files
     *
     * @access public
     * @param array $attachments
     * @return ObjectStorage<Attachment>
     */
    public function initAttachments(array $attachments)
    {
        $objAttachments = new ObjectStorage();
        foreach ($attachments as $singleAttachmentStack) {
            $this->initSingleAttachmentStack($singleAttachmentStack, $objAttachments);
        }
        return $objAttachments;
    }

    /**
     * @see initAttachments(array $attachments)
     *
     * @param $singleAttachmentStack
     * @param $objAttachments \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected function initSingleAttachmentStack($singleAttachmentStack, $objAttachments)
    {
        foreach ($singleAttachmentStack as $attachmentID => $singleAttachment) {
            $this->initSingleAttachment($singleAttachment, $attachmentID, $objAttachments);
        }
    }

    /**
     * Converts single file of $_FILES as \T3forum\T3forum\Domain\Model\Forum\Attachment,
     * saves this Attachment in ObjectStorage and moves the file to final location in file system
     *
     * @see initAttachments(array $attachments)
     *
     * @param $singleAttachmentStack
     * @param $attachmentID
     * @param $objAttachments \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected function initSingleAttachment(array $singleAttachment, $attachmentID, $objAttachments)
    {
        $requiredKeys = ['name', 'type', 'tmp_name'];
        foreach ($requiredKeys as $requiredKey) {
            if (!array_key_exists($requiredKey, $singleAttachment) || !$singleAttachment[$requiredKey]) {
                return;
            }
        }
        $tmp_name = $_FILES['tx_t3forum_pi1']['tmp_name']['attachments'][$attachmentID];

        /* @var \T3forum\T3forum\Domain\Model\Forum\Attachment */
        $attachmentObj = $this->objectManager->get(Attachment::class);
        $attachmentObj->setFilename($singleAttachment['name']);
        $attachmentObj->setRealFilename(sha1($singleAttachment['name'] . time()));

        //$attachmentObj->setMimeType(mime_content_type($tmp_name));
        $attachmentObj->setMimeType($singleAttachment['type']);

        // Create directory if it does not exist
        $tca = $attachmentObj->getTCAConfig();
        $path = $tca['columns']['real_filename']['config']['uploadfolder'];
        if (!file_exists($path)) {
            // @TODO fix permissions on base of setting in LocalConfiguration.php
            // (or another external setting)
            mkdir($path, 0664, true);
        }

        // Move uploaded file to final location in file system
        $res = GeneralUtility::upload_copy_move(
            $singleAttachment['tmp_name'],
            $attachmentObj->getAbsoluteFilename()
        );

        // @TODO: fix file-permissions on base of settings -> 0664 / 0644
        // Which settings, LocalConfiguration.php?
        // NO execution desired in forum!

        if ($res === true) {
            // Save in ObjectStorage
            $objAttachments->attach($attachmentObj);
        }
    }
}
