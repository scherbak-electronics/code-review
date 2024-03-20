<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\DownloadsImportExport\Helper;

use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory as SectionCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;

class DataProvider
{
    const CODE_ALL  = 'all';
    const DELIMITER = '|||';

    /**
     * @var StoreCollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var array|null
     */
    protected $storeCodes;

    /**
     * @var array|null
     */
    protected $sectionNames;

    /**
     * @var SectionCollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * @var ProductResourceModel
     */
    protected $productResourceModel;

    /**
     * @var array|null
     */
    protected $customerGroupCodes;

    /**
     * @var CustomerGroupCollectionFactory
     */
    protected $customerGroupCollectionFactory;

    /**
     * DataProvider constructor.
     *
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param SectionCollectionFactory $sectionCollectionFactory
     * @param ProductResourceModel $productResourceModel
     * @param CustomerGroupCollectionFactory $customerGroupCollectionFactory
     */
    public function __construct(
        StoreCollectionFactory $storeCollectionFactory,
        SectionCollectionFactory $sectionCollectionFactory,
        ProductResourceModel $productResourceModel,
        CustomerGroupCollectionFactory $customerGroupCollectionFactory
    ) {
        $this->storeCollectionFactory         = $storeCollectionFactory;
        $this->sectionCollectionFactory       = $sectionCollectionFactory;
        $this->productResourceModel           = $productResourceModel;
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    /**
     * @return array
     */
    public function getStoreCodes(): array
    {
        if (isset($this->storeCodes)) {
            return $this->storeCodes;
        }

        $this->storeCodes = [];
        $collection       = $this->storeCollectionFactory->create();
        $collection->addFieldToSelect(['store_id', 'code']);

        foreach ($collection->getData() as $datum) {
            $this->storeCodes[$datum['store_id']] = $datum['code'];
        }

        return $this->storeCodes;
    }

    /**
     * @param array $storeIds
     * @return string
     */
    public function getStoresAsString(array $storeIds): string
    {
        if (empty($storeIds)) {
            return '';
        }

        if (in_array(Store::DEFAULT_STORE_ID, $storeIds)) {
            return self::CODE_ALL;
        }

        $storeCodes = array_intersect_key($this->getStoreCodes(), array_flip($storeIds));

        return implode(self::DELIMITER, $storeCodes);
    }

    /**
     * @param string $storesAsString
     * @return array
     */
    public function getStoreCodesFromString(string $storesAsString): array
    {
        return explode(self::DELIMITER, $storesAsString);
    }

    /**
     * @param array $productIds
     * @return string
     */
    public function getProductsAsString(array $productIds): string
    {
        if (empty($productIds)) {
            return '';
        }

        $result = [];

        foreach ($this->productResourceModel->getProductsSku($productIds) as $data) {
            $result[] = $data['sku'];
        }

        return implode(self::DELIMITER, $result);
    }

    /**
     * @param string $productsAsString
     * @return array
     */
    public function getProductSkusFromString(string $productsAsString): array
    {
        return explode(self::DELIMITER, $productsAsString);
    }

    /**
     * @param array $customerGroupIds
     * @return string
     */
    public function getCustomerGroupsAsString(array $customerGroupIds): string
    {
        if (empty($customerGroupIds)) {
            return '';
        }

        if (!isset($this->customerGroupCodes)) {
            $collection               = $this->customerGroupCollectionFactory->create();
            $this->customerGroupCodes = $collection->toOptionHash();
        }

        $customerGroupCodes = array_intersect_key($this->customerGroupCodes, array_flip($customerGroupIds));

        return implode(self::DELIMITER, $customerGroupCodes);
    }

    /**
     * @param string $customerGroupsAsString
     * @return array
     */
    public function getCustomerGroupsFromString(string $customerGroupsAsString): array
    {
        return explode(self::DELIMITER, $customerGroupsAsString);
    }

    /**
     * @param int $sectionId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSectionName(int $sectionId): string
    {
        if (!isset($this->sectionNames)) {
            /** @var \MageWorx\Downloads\Model\ResourceModel\Section\Collection $collection */
            $collection = $this->sectionCollectionFactory->create();
            $collection->addLocales(Store::DEFAULT_STORE_ID);

            $names = [];

            /** @var \MageWorx\Downloads\Model\Section $section */
            foreach ($collection->getItems() as $section) {
                $names[$section->getSectionId()] = $section->getName();
            }

            $this->sectionNames = $names;
        }

        if (isset($this->sectionNames[$sectionId])) {
            return $this->sectionNames[$sectionId];
        }

        return '';
    }
}
