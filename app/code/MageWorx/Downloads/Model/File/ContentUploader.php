<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\File;

use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use MageWorx\Downloads\Api\Data\File\ContentInterface;
use MageWorx\Downloads\Model\Attachment as AttachmentConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class ContentUploader extends Uploader implements \MageWorx\Downloads\Api\Data\File\ContentUploaderInterface
{
    /**
     * Default MIME type
     */
    const DEFAULT_MIME_TYPE = 'application/octet-stream';

    /**
     * Filename prefix for temporary files
     *
     * @var string
     */
    protected $filePrefix = 'magento_api';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $systemTmpDirectory;

    /**
     * @var AttachmentConfig
     */
    protected $linkConfig;

    /**
     * @param Database $coreFileStorageDb
     * @param Storage $coreFileStorage
     * @param NotProtectedExtension $validator
     * @param Filesystem $filesystem
     * @param AttachmentConfig $linkConfig
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Database $coreFileStorageDb,
        Storage $coreFileStorage,
        NotProtectedExtension $validator,
        Filesystem $filesystem,
        AttachmentConfig $linkConfig
    ) {
        $this->_validator         = $validator;
        $this->_coreFileStorage   = $coreFileStorage;
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->mediaDirectory     = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->systemTmpDirectory = $filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $this->linkConfig         = $linkConfig;
    }

    /**
     * Decode base64 encoded content and save it in system tmp folder
     *
     * @param ContentInterface $fileContent
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function decodeContent(ContentInterface $fileContent)
    {
        $tmpFileName = $this->getTmpFileName();
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $fileSize    = $this->systemTmpDirectory->writeFile($tmpFileName, base64_decode($fileContent->getFileData()));
        // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage

        return [
            'name'     => $fileContent->getName(),
            'type'     => self::DEFAULT_MIME_TYPE,
            'tmp_name' => $this->systemTmpDirectory->getAbsolutePath($tmpFileName),
            'error'    => 0,
            'size'     => $fileSize,
        ];
    }

    /**
     * Generate temporary file name
     *
     * @return string
     */
    protected function getTmpFileName()
    {
        return uniqid($this->filePrefix, true);
    }

    /**
     * {@inheritdoc}
     */
    public function upload(ContentInterface $fileContent)
    {
        $this->_file = $this->decodeContent($fileContent);
        if (!file_exists($this->_file['tmp_name'])) {
            throw new \InvalidArgumentException('There was an error during file content upload.');
        }
        $this->_fileExists = true;
        $this->_uploadType = self::SINGLE_STYLE;
        $this->setAllowRenameFiles(true);
        $this->setFilesDispersion(true);
        $this->setAllowCreateFolders(true);
        $result = $this->save(
            $this->mediaDirectory->getAbsolutePath($this->linkConfig->getBaseTmpPath())
        );
        unset($result['path']);
        $result['status'] = 'new';
        $result['name']   = substr($result['file'], strrpos($result['file'], '/') + 1);

        return $result;
    }
}
