<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use MageWorx\Downloads\Api\Data\AttachmentInterface;
use Magento\Framework\Filesystem\Driver\File;
use MageWorx\Downloads\Model\Attachment\Link as AttachmentLinkModel;
use MageWorx\Downloads\Model\Attachment\Source\ContentType;
use MageWorx\Downloads\Api\Data\AttachmentLocaleInterface;
use MageWorx\Downloads\Api\Data\AttachmentLocaleInterfaceFactory;

class Attachment extends AbstractExtensibleModel implements AttachmentInterface, ComponentInterface
{
    /**
     * @var AttachmentLinkModel
     */
    protected $attachmentLinkModel;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var AttachmentLocaleInterfaceFactory
     */
    protected $attachmentLocaleFactory;

    /**
     * @var ResourceModel\Attachment
     */
    protected $attachmentResource;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * Attachment constructor.
     *
     * @param AttachmentLinkModel $attachmentLinkModel
     * @param File $file
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param AttachmentLocaleInterfaceFactory $attachmentLocaleFactory
     * @param ResourceModel\Attachment $attachmentResource
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        AttachmentLinkModel $attachmentLinkModel,
        File $file,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        AttachmentLocaleInterfaceFactory $attachmentLocaleFactory,
        \MageWorx\Downloads\Model\ResourceModel\Attachment $attachmentResource,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->attachmentLinkModel     = $attachmentLinkModel;
        $this->file                    = $file;
        $this->attachmentLocaleFactory = $attachmentLocaleFactory;
        $this->attachmentResource      = $attachmentResource;
        $this->storeManager            = $storeManager;
    }

    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mw_attachment';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mw_attachment';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_downloads_attachment';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\Downloads\Model\ResourceModel\Attachment::class);
    }

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return 'mageworx/downloads/tmp/attachment/file';
    }

    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public function getBasePath()
    {
        return 'mageworx/downloads/attachment/file';
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        $id = $this->getData(self::ID);

        if (!$id) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @return int|null
     */
    public function getSectionId(): ?int
    {
        $id = $this->getData(self::SECTION_ID);

        if (!$id) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @param int $value
     * @return AttachmentInterface
     */
    public function setSectionId(int $value): AttachmentInterface
    {
        return $this->setData(self::SECTION_ID, $value);
    }

    /**
     * @return bool|null
     */
    public function getIsAttach(): ?bool
    {
        return (bool)$this->getData(self::IS_ATTACH);
    }

    /**
     * @param bool $value
     * @return AttachmentInterface
     */
    public function setIsAttach(bool $value): AttachmentInterface
    {
        return $this->setData(self::IS_ATTACH, $value);
    }

    /**
     * Return file path or null when type is 'link'
     *
     * @return string|null relative file path
     */
    public function getFilename(): ?string
    {
        return (string)$this->getData(self::FILENAME);
    }

    /**
     * Set file path or null when type is 'link'
     *
     * @param string|null $value
     * @return AttachmentInterface
     */
    public function setFilename(?string $value): AttachmentInterface
    {
        return $this->setData(self::FILENAME, $value);
    }

    /**
     * Return link url or null when type is 'file'
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return (string)$this->getData(self::URL);
    }

    /**
     * Set URL
     *
     * @param string|null $value
     * @return AttachmentInterface
     */
    public function setUrl(?string $value): AttachmentInterface
    {
        return $this->setData(self::URL, $value);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return (string)$this->getData(self::TYPE);
    }

    /**
     * @param string $value
     * @return AttachmentInterface
     */
    public function setType(string $value): AttachmentInterface
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @return string|null
     */
    public function getFiletype(): ?string
    {
        return (string)$this->getData(self::FILETYPE);
    }

    /**
     * @param string $value
     * @return AttachmentInterface
     */
    public function setFiletype(string $value): AttachmentInterface
    {
        return $this->setData(self::FILETYPE, $value);
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return (int)$this->getData(self::SIZE);
    }

    /**
     * @param int $value
     * @return AttachmentInterface
     */
    public function setSize(int $value): AttachmentInterface
    {
        return $this->setData(self::SIZE, $value);
    }

    /**
     * Count of downloads
     *
     * @return int|null
     */
    public function getDownloads(): ?int
    {
        return (int)$this->getData(self::DOWNLOADS);
    }

    /**
     * Set count of downloads
     *
     * @param int|null $value
     * @return AttachmentInterface
     */
    public function setDownloads(?int $value): AttachmentInterface
    {
        return $this->setData(self::DOWNLOADS, $value);
    }

    /**
     * Count of possible downloads
     * 0 for unlimited downloads
     *
     * @return int|null
     */
    public function getDownloadsLimit(): ?int
    {
        return (int)$this->getData(self::DOWNLOADS_LIMIT);
    }

    /**
     * Set count of possible downloads
     * 0 for unlimited downloads
     *
     * @param int $value
     * @return AttachmentInterface
     */
    public function setDownloadsLimit(int $value): AttachmentInterface
    {
        return $this->setData(self::DOWNLOADS_LIMIT, $value);
    }

    /**
     * Return file content
     *
     * @return \MageWorx\Downloads\Api\Data\File\ContentInterface|null
     */
    public function getAttachmentFileContent()
    {
        return $this->getData(self::FILE_CONTENT);
    }

    /**
     * Set file content
     *
     * @param \MageWorx\Downloads\Api\Data\File\ContentInterface|null $linkFileContent
     * @return $this
     */
    public function setAttachmentFileContent(\MageWorx\Downloads\Api\Data\File\ContentInterface $linkFileContent = null)
    {
        return $this->setData(self::FILE_CONTENT, $linkFileContent);
    }

    /**
     * @return string|null
     */
    public function getDateModified(): ?string
    {
        return (string)$this->getData(self::DATE_MODIFIED);
    }

    /**
     * @param string $value
     * @return AttachmentInterface
     */
    public function setDateModified(string $value): AttachmentInterface
    {
        return $this->setData(self::DATE_MODIFIED, $value);
    }

    /**
     * @return string|null
     */
    public function getDateAdded(): ?string
    {
        return (string)$this->getData(self::DATE_ADDED);
    }

    /**
     * @param string $value
     * @return AttachmentInterface
     */
    public function setDateAdded(string $value): AttachmentInterface
    {
        return $this->setData(self::DATE_ADDED, $value);
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * @param bool $value
     * @return AttachmentInterface
     */
    public function setIsActive(bool $value): AttachmentInterface
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * Get a list of stores the attachment assigned to
     *
     * @return int[]|null
     */
    public function getStoreIds(): ?array
    {
        return $this->getData(self::STORES);
    }

    /**
     * Set the stores the attachment assigned to
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds(array $storeIds): AttachmentInterface
    {
        return $this->setData(self::STORES, $storeIds);
    }

    /**
     * Get display locales
     *
     * @return AttachmentLocaleInterface[]|null
     */
    public function getStoreLocales(): ?array
    {
        return $this->getData(self::STORE_LOCALES);
    }

    /**
     * Set display locales
     *
     * @param AttachmentLocaleInterface[]|null $storeLocales
     * @return self
     */
    public function setStoreLocales(array $storeLocales = null): AttachmentInterface
    {
        return $this->setData(self::STORE_LOCALES, $storeLocales);
    }

    /**
     * Get ids of customer groups
     *
     * @return int[]
     */
    public function getCustomerGroupIds(): ?array
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * Set the customer groups
     *
     * @param int[] $customerGroupIds
     * @return self
     */
    public function setCustomerGroupIds(array $customerGroupIds): AttachmentInterface
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customerGroupIds);
    }

    /**
     * Get product IDs
     *
     * @return int[]
     */
    public function getProductIds(): ?array
    {
        return $this->getData(self::PRODUCTS);
    }

    /**
     * Set product IDs
     *
     * @param int[]|null $productIds
     * @return self
     */
    public function setProductIds(?array $productIds): AttachmentInterface
    {
        return $this->setData(self::PRODUCTS, $productIds);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|\MageWorx\Downloads\Api\Data\AttachmentExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\Downloads\Api\Data\AttachmentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'section_id'  => Section::DEFAULT_ID,
            'assign_type' => 1,
            'is_active'   => self::STATUS_DISABLED
        ];
    }

    /**
     * @return void
     */
    public function clearAttachment()
    {
        $this->setData('filename', '');
        $this->setData('filetype', '');
    }

    /**
     * @return boolean
     * @todo replace and remove
     * @deprecared
     */
    public function getContentType()
    {
        return $this->getType();
    }

    /**
     *
     * @return boolean
     */
    public function isFileContent()
    {
        return ContentType::CONTENT_FILE == $this->getType();
    }

    /**
     * @return boolean
     */
    public function isUrlContent()
    {
        $type = $this->getContentType();

        return ContentType::CONTENT_URL == $type;
    }

    /**
     * @return Attachment
     * @throws FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSave()
    {
        if ($this->isFileContent() && $this->getFilename()) {
            if (file_exists($this->attachmentLinkModel->getBaseDir() . $this->getFilename())) {
                $fileType = substr($this->getFilename(), strrpos($this->getFilename(), '.') + 1);
                $fileSize = filesize($this->attachmentLinkModel->getBaseDir() . $this->getFilename());

                $this->setFiletype($fileType);
                $this->setSize($fileSize);

                if (!$this->getName()) {
                    $parts = explode(DIRECTORY_SEPARATOR, $this->getFilename());
                    $this->setDefaultName(array_pop($parts));
                }
            }
        } elseif ($this->isUrlContent() && $this->getFilename()) {
            $this->deleteFile();
            $this->setFiletype('');
            $this->setSize(0);
        }

        return parent::beforeSave();
    }

    /**
     * @return Attachment
     */
    protected function _afterLoad()
    {
        $this->loadRelations();

        return parent::_afterLoad();
    }

    /**
     * @return $this
     */
    public function loadRelations()
    {
        if ($this->getId()) {
            $this->loadStoreIds();
            $this->loadCustomerGroupIds();
            $this->loadProductIds();
            $this->loadStoreLocales();
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function loadStoreIds(): array
    {
        $stores = $this->attachmentResource->lookupStoreIds($this->getId());
        $this->setStoreIds($stores);

        return $stores;
    }

    /**
     * @return array
     */
    protected function loadCustomerGroupIds(): array
    {
        $customerGroups = $this->attachmentResource->lookupCustomerGroupIds($this->getId());
        $this->setCustomerGroupIds($customerGroups);

        return $customerGroups;
    }

    /**
     * @return array
     */
    protected function loadProductIds(): array
    {
        $productIds = $this->attachmentResource->getProducts($this);
        $this->setProductIds($productIds);

        return $productIds;
    }

    /**
     * @return \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface[]
     */
    protected function loadStoreLocales(): array
    {
        $locales = $this->attachmentResource->getExistsStoreLocaleData($this->getId());

        $storeLocales = [];

        foreach ($locales as $storeData) {
            /** @var \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface $storeLocale */
            $storeLocale = $this->attachmentLocaleFactory->create();
            $storeLocale->setStoreId($storeData['store_id']);
            $storeLocale->setStoreName($storeData['name']);
            $storeLocale->setStoreDescription($storeData['description']);
            $storeLocales[] = $storeLocale;
        }

        $this->setStoreLocales($storeLocales);

        return $storeLocales;
    }

    /**
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getName($storeId = null): string
    {
        if ($this->hasData('name')) {
            return (string)$this->getData('name');
        }

        if ($this->name === null) {
            $storeId      = $storeId ?? $this->storeManager->getStore()->getId();
            $storeLocales = $this->getStoreLocales();
            $name         = $defaultName = '';

            if ($storeLocales) {
                foreach ($storeLocales as $storeLocale) {
                    if ((int)$storeLocale->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                        $defaultName = $storeLocale->getStoreName();
                    } elseif ($storeLocale->getStoreId() == $storeId) {
                        $name = $storeLocale->getStoreName();
                    }
                }
            }

            $this->name = $name ?: $defaultName;
        }

        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setDefaultName($name)
    {
        $storeLocales = $this->getStoreLocales();

        if ($storeLocales) {
            foreach ($storeLocales as $storeLocale) {
                if ($storeLocale->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    $storeLocale->setStoreName($name);
                    $this->name = null;
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDescription($storeId = null): string
    {
        if ($this->hasData('description')) {
            return (string)$this->getData('description');
        }

        if ($this->description === null) {
            $storeId      = $storeId ?? $this->storeManager->getStore()->getId();
            $storeLocales = $this->getStoreLocales();
            $description  = $defaultDescription = '';

            foreach ($storeLocales as $storeLocale) {
                if ($storeLocale->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    $defaultDescription = $storeLocale->getStoreDescription();
                } elseif ($storeLocale->getStoreId() == $storeId) {
                    $description = $storeLocale->getStoreDescription();
                }
            }

            $this->description = $description ?: $defaultDescription;
        }

        return $this->description;
    }

    /**
     * @return int
     */
    public function getDownloadsLeft(): int
    {
        $downloadsLeft = $this->getDownloadsLimit() - $this->getDownloads();

        return ($downloadsLeft < 0) ? 0 : $downloadsLeft;
    }

    /**
     * @return AbstractModel
     * @throws FileSystemException
     */
    public function afterDelete()
    {
        $this->deleteFile();

        return parent::afterDelete();
    }

    /**
     * @throws FileSystemException
     */
    protected function deleteFile()
    {
        if ($this->getFilename()) {
            $path = rtrim($this->attachmentLinkModel->getBaseDir(), '/') . '/' . ltrim($this->getFilename(), '/');

            if ($this->file->isExists($path)) {
                $this->file->deleteFile($path);
            }
        }
    }
}
