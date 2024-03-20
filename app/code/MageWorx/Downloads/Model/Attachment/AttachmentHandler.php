<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Model\Attachment;

use Magento\Downloadable\Helper\File;
use MageWorx\Downloads\Model\ComponentInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ObjectManager;

class AttachmentHandler
{
    const FIELD_IS_DELETE = 'is_delete';

    const FIELD_FILE = 'file';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var File
     */
    protected $downloadableFile;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var array
     */
    protected $deletedItems = [];

    /**
     * AttachmentHandler constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param File $downloadableFile
     * @param \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory
     * @param \MageWorx\Downloads\Model\ResourceModel\Attachment $attachmentResource
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        \MageWorx\Downloads\Model\ResourceModel\Attachment $attachmentResource
    ) {
        $this->jsonSerializer     = $jsonSerializer;
        $this->downloadableFile   = $downloadableFile;
        $this->attachmentFactory  = $attachmentFactory;
        $this->attachmentResource = $attachmentResource;

    }

    /**
     * {@inheritdoc}
     */
    public function getDataKey()
    {
        return 'attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierKey()
    {
        return 'attachment_id';
    }

    /**
     * @param array $data
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface
     */
    public function save(array $data)
    {
        $this->clear();

        foreach ($data['attachment'] as $item) {
            if ($this->isDelete($item)) {
                $this->addToDeleteQueue($item);
            } else {
                $model = $this->saveItem($item);
            }
        }
        $this->processDelete();

        return $model;
    }

    /**
     * @return ComponentInterface
     */
    protected function createItem()
    {
        return $this->attachmentFactory->create();
    }

    /**
     * @param ComponentInterface $model
     * @param array $data
     * @return void
     */
    protected function setDataToModel(ComponentInterface $model, array $data)
    {
        $model->setData(
            $data
        );
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareItem(array $item)
    {
        unset($item[self::FIELD_IS_DELETE], $item[self::FIELD_FILE]);
        if (isset($item[$this->getIdentifierKey()]) && !$item[$this->getIdentifierKey()]) {
            unset($item[$this->getIdentifierKey()]);
        }

        return $item;
    }

    /**
     * @return void
     */
    protected function processDelete()
    {
        if ($this->deletedItems) {
            $this->attachmentResource->deleteItems($this->deletedItems);
        }
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function isDelete(array $item)
    {
        return isset($item[self::FIELD_IS_DELETE]) && '1' == $item[self::FIELD_IS_DELETE];
    }

    /**
     * @param array $item
     * @return array
     */
    protected function getFiles(array $item)
    {
        $files = [];
        if (isset($item[self::FIELD_FILE]) && $item[self::FIELD_FILE]) {
            $files = $this->jsonSerializer->unserialize($item[self::FIELD_FILE]);
        }

        return $files;
    }

    /**
     * @param ComponentInterface $model
     * @param array $files
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function setFiles(ComponentInterface $model, array $files)
    {
        if ($model->getType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE) {

            $filename = $this->downloadableFile->moveFileFromTmp(
                $model->getBaseTmpPath(),
                $model->getBasePath(),
                $files
            );

            $model->setFilename($filename);

            if (!empty($files[0]['size'])) {
                $model->setSize($files[0]['size']);
            }
        }
    }

    /**
     * @param array $item
     * @return void
     */
    protected function addToDeleteQueue(array $item)
    {
        if (!empty($item[$this->getIdentifierKey()])) {
            $this->deletedItems[] = $item[$this->getIdentifierKey()];
        }
    }

    /**
     * Get MetadataPool instance
     *
     * @return MetadataPool
     */
    protected function getMetadataPool()
    {
        if (!$this->metadataPool) {
            $this->metadataPool = ObjectManager::getInstance()->get(MetadataPool::class);
        }

        return $this->metadataPool;
    }

    /**
     * @var \MageWorx\Downloads\Model\ComponentInterface
     */
    private $attachmentFactory;

    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Attachment
     */
    private $attachmentResource;

    /**
     * @return void
     */
    protected function clear()
    {
        $this->deletedItems = [];
    }

    /**
     * @param array $item
     * @return ComponentInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveItem(array $item)
    {
        $files = $this->getFiles($item);
        $item  = $this->prepareItem($item);

        $model = $this->createItem();

        $this->setDataToModel($model, $item);
        $this->setFiles($model, $files);

        $this->attachmentResource->save($model);

        return $model;
    }
}
