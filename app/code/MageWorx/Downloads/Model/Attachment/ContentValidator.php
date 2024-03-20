<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\Attachment;

use MageWorx\Downloads\Api\Data\AttachmentInterface;
use Magento\Downloadable\Helper\File;
use MageWorx\Downloads\Model\File\ContentValidator as FileContentValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Url\Validator as UrlValidator;
use Magento\Downloadable\Model\Url\DomainValidator;

/**
 * Class to validate Link Content.
 */
class ContentValidator
{
    /**
     * @var DomainValidator
     */
    private $domainValidator;

    /**
     * @var File
     */
    private $fileHelper;

    /**
     * @var FileContentValidator
     */
    protected $fileContentValidator;

    /**
     * @var UrlValidator
     */
    protected $urlValidator;

    /**
     * @param FileContentValidator $fileContentValidator
     * @param UrlValidator $urlValidator
     * @param DomainValidator $domainValidator
     * @param File|null $fileHelper
     */
    public function __construct(
        FileContentValidator $fileContentValidator,
        UrlValidator $urlValidator,
        DomainValidator $domainValidator,
        File $fileHelper
    ) {
        $this->fileContentValidator = $fileContentValidator;
        $this->urlValidator         = $urlValidator;
        $this->domainValidator      = $domainValidator;
        $this->fileHelper           = $fileHelper;
    }

    /**
     * Check if link content is valid.
     *
     * @param AttachmentInterface $attachment
     * @param bool $validateLinkContent
     * @return bool
     * @throws InputException
     */
    public function isValid(AttachmentInterface $attachment, $validateLinkContent = true)
    {
        if (filter_var($attachment->getDownloadsLimit(), FILTER_VALIDATE_INT) === false
            || $attachment->getDownloadsLimit() < 0) {
            throw new InputException(__('Downloads limit must be a positive integer.'));
        }

        if (filter_var($attachment->getDownloadsLimit(), FILTER_VALIDATE_INT) === false
            || $attachment->getDownloadsLimit() < 0) {
            throw new InputException(__('Downloads must be a positive integer.'));
        }

        if ($validateLinkContent) {
            $this->validateLinkResource($attachment);
        }

        return true;
    }

    /**
     * Validate attachment resource (file or URL).
     *
     * @param AttachmentInterface $attachment
     * @return void
     * @throws InputException
     */
    protected function validateLinkResource(AttachmentInterface $attachment)
    {
        if ($attachment->getUrl()) {
            if (!$this->urlValidator->isValid($attachment->getUrl())) {
                throw new InputException(__('Link URL must have valid format.'));
            }
        } elseif ($attachment->getAttachmentFileContent()) {
            if (!$this->fileContentValidator->isValid($attachment->getAttachmentFileContent())) {
                throw new InputException(__('Provided file content must be valid base64 encoded data.'));
            }
        } elseif (!$this->isFileValid($attachment->getBasePath() . $attachment->getFilename())) {
            throw new InputException(__('Attachment file not found. Please try again.'));
        }
    }

    /**
     * Check that Links File or Sample is valid.
     *
     * @param string $file
     * @return bool
     */
    private function isFileValid(string $file): bool
    {
        try {
            return $this->fileHelper->ensureFileInFilesystem($file);
        } catch (ValidatorException $e) {
            return false;
        }
    }
}
