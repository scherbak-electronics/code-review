<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use MageWorx\Downloads\Api\Data\AttachmentInterface;
use MageWorx\Downloads\Api\Data\AttachmentLinkInterface;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;
use MageWorx\Downloads\Model\Attachment\Source\FileSize;

class AttachmentLinkBuilder
{
    /**
     * @var \MageWorx\Downloads\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\Downloads\Api\Data\AttachmentLinkInterfaceFactory
     */
    protected $attachmentLinkFactory;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AttachmentLinkBuilder constructor.
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentLinkInterfaceFactory $attachmentLinkFactory
     * @param \MageWorx\Downloads\Helper\Data $helperData
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param Filesystem $filesystem
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\Downloads\Api\Data\AttachmentLinkInterfaceFactory $attachmentLinkFactory,
        \MageWorx\Downloads\Helper\Data $helperData,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        Filesystem $filesystem,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->helperData            = $helperData;
        $this->attachmentLinkFactory = $attachmentLinkFactory;
        $this->filesystem            = $filesystem;
        $this->assetRepo             = $assetRepo;
        $this->urlBuilder            = $urlBuilder;
        $this->storeManager          = $storeManager;
    }

    /**
     * @param AttachmentInterface $resourceAttachment
     * @return AttachmentLinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(AttachmentInterface $resourceAttachment): AttachmentLinkInterface
    {
        /** @var AttachmentLinkInterface $attachmentLink */
        $attachmentLink = $this->attachmentLinkFactory->create();

        $attachmentLink->setId($resourceAttachment->getId());
        $attachmentLink->setType($resourceAttachment->getType());
        $attachmentLink->setName($resourceAttachment->getName());
        $attachmentLink->setDescription($resourceAttachment->getDescription());
        $attachmentLink->setSectionId($resourceAttachment->getSectionId());
        $attachmentLink->setSectionName($resourceAttachment->getSectionName());
        $attachmentLink->setSectionDescription($resourceAttachment->getSectionDescription());
        $attachmentLink->setLink($this->getAttachmentLink($resourceAttachment->getId()));

        if ($resourceAttachment->getDownloadsLimit()) {

            $downloadsLeft = max(
                0,
                $resourceAttachment->getDownloadsLimit() - $resourceAttachment->getDownloads()
            );

            $attachmentLink->setDownloadsLeft($downloadsLeft);
        }

        if ($this->helperData->isDisplaySize()
            && $resourceAttachment->getType() == ContentType::CONTENT_FILE
        ) {
            $attachmentLink->setSize($this->getPrepareFileSize($resourceAttachment->getSize()));
        }

        return $attachmentLink;
    }

    /**
     * @param int $attachmentId
     * @return string
     */
    protected function getAttachmentLink(int $attachmentId): string
    {
        return $this->urlBuilder->getUrl('mwdownloads/download/link', ['id' => $attachmentId]);
    }

    /**
     * Retrieve attachment icon URL
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentLinkInterface $attachmentLink
     * @return string
     */
    protected function getIconUrl(\MageWorx\Downloads\Api\Data\AttachmentLinkInterface $attachmentLink): string
    {
        $name = $attachmentLink->getFiletype();

        if ($attachmentLink->getType() == 'link') {
            if (strripos($name, 'youtube.com')) {
                $name = 'youtube';
            } else {
                return '';
            }
        }

        $iconName = strtolower($name) . '.png';
        $iconUrl  = $this->assetRepo->getUrl('MageWorx_Downloads::images/filetypes/' . $iconName);

        if (!$this->isImageExists($iconUrl)) {
            $iconName = 'default.png';
            $iconUrl  = $this->assetRepo->getUrl('MageWorx_Downloads::images/filetypes/' . $iconName);
        }

        return $iconUrl;
    }

    /**
     * Retrieve formatted string
     *
     * @param int $size
     * @return string
     */
    protected function getPrepareFileSize(int $size): string
    {
        $round  = 1;
        $b      = __('B');
        $kb     = __('KB');
        $mb     = __('MB');
        $kbSize = 1024;
        $mbSize = $kbSize * $kbSize;

        switch ($this->helperData->getSizePrecision()) {
            case FileSize::FILE_SIZE_PRECISION_AUTO:
                if ($size >= $kbSize && $size < $mbSize) {
                    $parsedSize = $size / $kbSize;
                    $type       = $kb;
                } elseif ($size >= $mbSize) {
                    $parsedSize = $size / $mbSize;
                    $type       = $mb;
                } else {
                    $parsedSize = $size;
                    $type       = $b;
                    $round      = 0;
                }
                break;

            case FileSize::FILE_SIZE_PRECISION_MEGA:
                $parsedSize = $size / $mbSize;
                $type       = $mb;
                $round      = 2;
                break;

            case FileSize::FILE_SIZE_PRECISION_KILO:
                $parsedSize = $size / $kbSize;
                $type       = $kb;
                break;

            default:
                $parsedSize = $size;
                $type       = $b;
                $round      = 0;
                break;
        }

        return round($parsedSize, $round) . ' ' . $type;
    }

    /**
     *
     * @param string $iconUrl
     * @return boolean
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function isImageExists(string $iconUrl): bool
    {
        $iconUrl = preg_replace('/\/version[0-9]*/', '', $iconUrl);

        if (strpos($iconUrl, '/' . DirectoryList::STATIC_VIEW . '/') !== false) {
            $length       = strpos($iconUrl, '/' . DirectoryList::STATIC_VIEW . '/')
                + strlen(DirectoryList::STATIC_VIEW)
                + 2;
            $path         = substr_replace($iconUrl, '', 0, $length);
            $absolutePath = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW)->getAbsolutePath($path);
            if (file_exists($absolutePath)) {
                return true;
            }
        }

        return false;
    }
}
