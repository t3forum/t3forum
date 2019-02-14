<?php
namespace Mittwald\Typo3Forum\Service;

use Mittwald\Typo3Forum\Domain\Model\Forum\Attachment;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AttachmentService implements SingletonInterface
{
    /**
     * Instance of the Extbase object manager.
     *
     * @access protected
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager = null;

    /**
     * Converts array of sent $_FILES to an ObjectStorage wizth object(s) of type
     * \Mittwald\Typo3Forum\Domain\Model\Forum\Attachment and moves the files
     *
     * @access public
     *
     * @param array $attachments
     *
     * @return ObjectStorage<\Mittwald\Typo3Forum\Domain\Model\Forum\Attachment>
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
     * Converts single file of $_FILES as \Mittwald\Typo3Forum\Domain\Model\Forum\Attachment,
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
        $tmp_name = $_FILES['tx_typo3forum_pi1']['tmp_name']['attachments'][$attachmentID];

        /* @var \Mittwald\Typo3Forum\Domain\Model\Forum\Attachment */
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
