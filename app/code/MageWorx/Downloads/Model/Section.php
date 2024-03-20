<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use MageWorx\Downloads\Api\Data\SectionLocaleInterface;
use MageWorx\Downloads\Api\Data\SectionInterface;
use MageWorx\Downloads\Api\Data\SectionLocaleInterfaceFactory;

class Section extends \Magento\Framework\Model\AbstractExtensibleModel implements SectionInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mw_section';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mw_section';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_downloads_section';

    /**
     * @var ResourceModel\Section
     */
    protected $sectionResource;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SectionLocaleInterfaceFactory
     */
    protected $sectionLocaleFactory;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    public function __construct(
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        SectionLocaleInterfaceFactory $sectionLocaleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
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
        $this->sectionResource      = $sectionResource;
        $this->sectionLocaleFactory = $sectionLocaleFactory;
        $this->storeManager         = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\Downloads\Model\ResourceModel\Section::class);
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
        $id = $this->getData(self::ID);

        if (!$id) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @param int $value
     * @return self
     */
    public function setSectionId(int $value): self
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get display locales
     *
     * @return SectionLocaleInterface[]|null
     */
    public function getStoreLocales(): ?array
    {
        return $this->getData(self::STORE_LOCALES);
    }

    /**
     * Set display locales
     *
     * @param SectionLocaleInterface[]|null $storeLocales
     * @return SectionInterface
     */
    public function setStoreLocales(array $storeLocales = null): SectionInterface
    {
        return $this->setData(self::STORE_LOCALES, $storeLocales);
    }

    /**
     * @return Attachment|Section
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
            $this->loadStoreLocales();
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function loadStoreLocales(): array
    {
        $locales = $this->sectionResource->getExistsStoreLocalesData($this->getId());

        $storeLocales = [];

        foreach ($locales as $storeData) {
            /** @var \MageWorx\Downloads\Api\Data\SectionLocaleInterface $storeLocale */
            $storeLocale = $this->sectionLocaleFactory->create();
            $storeLocale->setStoreId($storeData['store_id']);
            $storeLocale->setStoreName($storeData['name']);
            $storeLocale->setStoreDescription($storeData['description']);
            $storeLocales[] = $storeLocale;
        }

        $this->setStoreLocales($storeLocales);

        return $storeLocales;
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
     * @return SectionInterface
     */
    public function setIsActive(bool $value): SectionInterface
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|\MageWorx\Downloads\Api\Data\SectionExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \MageWorx\Downloads\Api\Data\SectionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\MageWorx\Downloads\Api\Data\SectionExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return [self::IS_ACTIVE => self::STATUS_DISABLED];
    }

    /**
     *
     * @return \MageWorx\Downloads\Model\Section
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->getId() == self::DEFAULT_ID) {
            return $this;
        }
        $this->getResource()->delete($this);
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

                    if ($storeLocale->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
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
     * @param string|null $value
     */
    public function setDescription(?string $value): void
    {
        $this->description = $value;
    }
}
