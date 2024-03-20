<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Model;

use MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory as SectionCollectionFactory;
use MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Collection as AttachmentCollection;
use MageWorx\Downloads\Model\Attachment\Source\IsActive;
use MageWorx\DownloadsImportExport\Model\Attachment\Link as FileLinkModel;
use MageWorx\DownloadsImportExport\Helper\DataProvider;
use MageWorx\Downloads\Api\Data\AttachmentInterface;

class AttachmentCsvImportHandler
{
    const NAME_KEY        = 'name_key';
    const DESCRIPTION_KEY = 'description_key';

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var IsActive
     */
    protected $attachmentStatusOptions;

    /**
     * @var SectionCollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Section\Collection
     */
    protected $sectionCollection;

    /**
     * @var \MageWorx\Downloads\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Attachment
     */
    protected $attachmentResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var FileLinkModel
     */
    protected $fileLinkModel;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $customerGroupCollectionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \MageWorx\Downloads\Api\Data\AttachmentLocaleInterfaceFactory
     */
    protected $attachmentLocaleFactory;

    /**
     * @var AttachmentCollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var array
     */
    protected $missedFileNames = [];

    /**
     * @var bool
     */
    protected $isSkipMissedProducts;

    /**
     * @var bool
     */
    protected $isSkipMissedFiles;

    /**
     * @var array
     */
    protected $storeLocaleColumnsData = [];

    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Copy
     */
    protected $copyModel;

    /**
     * AttachmentCsvImportHandler constructor.
     *
     * @param IsActive $sectionStatusOptions
     * @param SectionCollectionFactory $sectionCollectionFactory
     * @param \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory
     * @param FileLinkModel $fileLinkModel
     * @param \MageWorx\Downloads\Model\ResourceModel\Attachment $attachmentResource
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     * @param Copy $copyModel
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \MageWorx\Downloads\Api\Data\AttachmentLocaleInterfaceFactory $attachmentLocaleFactory
     * @param AttachmentCollectionFactory $attachmentCollectionFactory
     * @param DataProvider $dataProvider
     */
    public function __construct(
        \MageWorx\Downloads\Model\Attachment\Source\IsActive $sectionStatusOptions,
        SectionCollectionFactory $sectionCollectionFactory,
        \MageWorx\Downloads\Model\AttachmentFactory $attachmentFactory,
        \MageWorx\DownloadsImportExport\Model\Attachment\Link $fileLinkModel,
        \MageWorx\Downloads\Model\ResourceModel\Attachment $attachmentResource,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory,
        \MageWorx\DownloadsImportExport\Model\Copy $copyModel,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\File\Csv $csvProcessor,
        \MageWorx\Downloads\Api\Data\AttachmentLocaleInterfaceFactory $attachmentLocaleFactory,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        DataProvider $dataProvider
    ) {
        $this->attachmentStatusOptions        = $sectionStatusOptions;
        $this->sectionCollectionFactory       = $sectionCollectionFactory;
        $this->attachmentFactory              = $attachmentFactory;
        $this->fileLinkModel                  = $fileLinkModel;
        $this->attachmentResource             = $attachmentResource;
        $this->productResource                = $productResource;
        $this->storeManager                   = $storeManager;
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->copyModel                      = $copyModel;
        $this->eventManager                   = $eventManager;
        $this->escaper                        = $escaper;
        $this->csvProcessor                   = $csvProcessor;
        $this->attachmentLocaleFactory        = $attachmentLocaleFactory;
        $this->attachmentCollectionFactory    = $attachmentCollectionFactory;
        $this->dataProvider                   = $dataProvider;
    }

    /**
     * @param array $file file info retrieved from $_FILES array
     * @param bool $isSkipMissedProducts
     * @param bool $isSkipMissedFiles
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file, $isSkipMissedProducts = false, $isSkipMissedFiles = false)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $data = $this->csvProcessor->getData($file['tmp_name']);

        if (count($data) < 2) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Data for import not found.')
            );
        }

        $this->isSkipMissedProducts = $isSkipMissedProducts;
        $this->isSkipMissedFiles    = $isSkipMissedFiles;

        $this->prepareStoreLocaleColumnsData($data[0]);

        array_shift($data);
        array_walk_recursive($data, [$this, 'trim']);

        $validatedData = $this->getValidatedData($data);
        $this->import($validatedData);
    }

    /**
     * @param string $item
     * @param string $key
     */
    protected function trim(
        &$item,
        $key
    ) {
        $item = trim($item);
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getValidatedData(array $data)
    {
        $formattedData = $this->validateByDataFormat($data);
        $validatedData = $this->validateByDataValues($formattedData);

        return $validatedData;
    }

    /**
     * Validate and format data
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateByDataFormat(array $data)
    {
        $formattedData = [];

        foreach ($data as $rowIndex => $dataRow) {

            if (!array_key_exists(0, $dataRow) || $dataRow[0] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed section name (title) in line %1', $rowIndex + 2)
                );
            }

            if ((!array_key_exists(2, $dataRow) || $dataRow[2] === '')
                && (!array_key_exists(3, $dataRow) || $dataRow[3] === '')
            ) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("File path and URL can't be empty at same time - see line %1", $rowIndex + 2)
                );
            }

            if (array_key_exists(2, $dataRow) && $dataRow[2] && array_key_exists(3, $dataRow) && $dataRow[3]) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("File path and URL can't be set at same time - see line %1", $rowIndex + 2)
                );
            }

            if (!array_key_exists(6, $dataRow) || $dataRow[6] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed customer group(s) in line %1', $rowIndex + 2)
                );
            }

            if (!array_key_exists(7, $dataRow) || $dataRow[7] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed store code(s) in line %1', $rowIndex + 2)
                );
            }

            if (array_key_exists(8, $dataRow) && $dataRow[8] !== '' && !is_numeric($dataRow[8])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Incorrect downloads limit in line %1', $rowIndex + 2)
                );
            }

            $statusOptions = array_map(
                'strval',
                array_column($this->attachmentStatusOptions->toOptionArray(), 'value')
            );

            if (!array_key_exists(9, $dataRow) || !in_array($dataRow[9], $statusOptions)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed or incorrect status in line %1', $rowIndex + 2)
                );
            }

            $formattedData[$rowIndex] = [
                'section_name'    => $dataRow[0],
                'name'            => $dataRow[1],
                'filepath'        => $dataRow[2],
                'url'             => $dataRow[3],
                'description'     => $dataRow[4],
                'product_skus'    => $dataRow[5],
                'customer_groups' => $dataRow[6],
                'store_codes'     => $dataRow[7],
                'downloads_limit' => $dataRow[8],
                'status'          => $dataRow[9],
            ];

            $this->addStoreLocaleDataToValidRowData($formattedData, $rowIndex, $dataRow);
        }

        return $formattedData;
    }

    /**
     * Validate and prepare data
     *
     * @param array $data
     * @return mixed
     */
    protected function validateByDataValues($data)
    {
        $this->validateByProducts($data);
        $this->validateBySection($data);
        $this->validateByStores($data);
        $this->validateByCustomerGroups($data);
        $this->validateByFilePath($data);

        return $data;
    }

    /**
     * @param array $data
     */
    protected function validateByProducts(array &$data)
    {
        $requestedSkus = [];
        foreach ($data as $key => $datum) {
            $requestedSkus = array_merge(
                $requestedSkus,
                $this->dataProvider->getProductSkusFromString($datum['product_skus'])
            );
        }

        $requestedSkus = array_filter($requestedSkus);

        $idSkuPairs = $this->productResource->getProductsIdsBySkus($requestedSkus);

        $missedSkus = array_diff($requestedSkus, array_flip($idSkuPairs));

        if (!$this->isSkipMissedProducts && $missedSkus) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested product SKUs are not found: %1.',
                    $this->escaper->escapeHtml(implode(', ', $missedSkus))
                )
            );
        }

        foreach ($data as $key => $datum) {
            $datumProductSkus                = $this->dataProvider->getProductSkusFromString($datum['product_skus']);
            $data[$key]['valid_product_ids'] = array_intersect_key($idSkuPairs, array_flip($datumProductSkus));
        }
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateBySection(array &$data)
    {
        $missedSection = [];

        foreach ($data as $key => $datum) {
            $section = $this->getSectionIdByName($datum['section_name']);

            if (!$section) {
                $missedSection[] = $datum['section_name'];
                continue;
            }
            $data[$key]['section_id'] = $section->getId();
        }

        $missedSection = array_unique($missedSection);

        if ($missedSection) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested sections are not found: %1.',
                    $this->escaper->escapeHtml(implode(', ', $missedSection))
                )
            );
        }
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateByStores(array &$data)
    {
        $storeCodeIdPairs = [];

        foreach ($this->storeManager->getStores(true, true) as $storeCode => $store) {
            $storeCodeIdPairs[$storeCode] = (string)$store->getId();
        }
        $missedStoreCodes = [];

        foreach ($data as $key => $datum) {
            $datumStoreCodes = $this->dataProvider->getStoreCodesFromString($datum['store_codes']);

            foreach ($datumStoreCodes as $datumKey => $storeCode) {

                if ($storeCode === DataProvider::CODE_ALL) {
                    $data[$key]['valid_store_ids'] = ['admin' => $storeCodeIdPairs['admin']];
                } elseif (array_key_exists($storeCode, $storeCodeIdPairs)) {
                    $data[$key]['valid_store_ids'][$storeCode] = $storeCodeIdPairs[$storeCode];
                } else {
                    $missedStoreCodes[$storeCode] = $storeCode;
                }
            }
        }

        if ($missedStoreCodes) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested store codes are not found: %1.',
                    $this->escaper->escapeHtml(implode(', ', $missedStoreCodes))
                )
            );
        }
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateByFilePath(array &$data)
    {
        $missedFiles = [];
        $fileNames   = $this->getExistingFileNames(array_filter(array_unique(array_column($data, 'filepath'))));

        foreach ($data as $datum) {
            $shortFilePathWithName = $datum['filepath'];

            if (!$shortFilePathWithName) {
                continue;
            }

            if (in_array($shortFilePathWithName, $fileNames)) {
                continue;
            }

            $oldFullPath = $this->fileLinkModel->getImportDir() . DIRECTORY_SEPARATOR . $shortFilePathWithName;

            if (!file_exists($oldFullPath)) {
                $missedFiles[] = $oldFullPath;
            }
        }

        if ($missedFiles && !$this->isSkipMissedFiles) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested file names are not found: %1.',
                    $this->escaper->escapeHtml(implode(', ', $missedFiles))
                )
            );
        }

        $this->missedFileNames = $missedFiles;
    }

    /**
     * @param array $fileNames
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getExistingFileNames(array $fileNames): array
    {
        if (empty($fileNames)) {
            return [];
        }

        $connection = $this->attachmentResource->getConnection();
        $select     = $connection->select();
        $select
            ->from($this->attachmentResource->getMainTable(), [AttachmentInterface::FILENAME])
            ->where(
                AttachmentInterface::TYPE . ' = ?',
                \MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_FILE
            )
            ->where(AttachmentInterface::FILENAME . ' IN(?)', $fileNames);

        return $connection->fetchCol($select);
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateByCustomerGroups(array &$data)
    {
        $groupNameIdPairs = [];

        /** @var \Magento\Customer\Model\ResourceModel\Group\Collection $group */
        $customerGroupCollection = $this->customerGroupCollectionFactory->create();

        /** @var \Magento\Customer\Model\Group $group */
        foreach ($customerGroupCollection as $group) {
            $groupNameIdPairs[$group->getCode()] = $group->getId();
        }

        $missedGroupCodes = [];

        foreach ($data as $key => $datum) {
            $datumGroupCodes = $this->dataProvider->getCustomerGroupsFromString($datum['customer_groups']);

            foreach ($datumGroupCodes as $datumKey => $groupCode) {

                if ($groupCode === DataProvider::CODE_ALL) {
                    $data[$key]['valid_customer_group_ids'] = $groupNameIdPairs;
                } elseif (array_key_exists($groupCode, $groupNameIdPairs)) {
                    $data[$key]['valid_customer_group_ids'][$groupCode] = $groupNameIdPairs[$groupCode];
                } else {
                    $missedGroupCodes[$groupCode] = $groupCode;
                }
            }
        }

        if ($missedGroupCodes) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'The requested customer groups are not found: %1.',
                    $this->escaper->escapeHtml(implode(', ', $missedGroupCodes))
                )
            );
        }
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    protected function import(array $data)
    {
        $collection = $this->getAttachmentCollection($data);

        foreach ($data as $datum) {
            if ($datum['url']) {
                $attachment = $this->getUrlAttachment($collection, $datum);
            } else {
                $attachment = $this->getFileAttachment($collection, $datum);
            }

            if (empty($attachment)) {
                continue;
            }

            $attachment
                ->setProductIds($datum['valid_product_ids'])
                ->setSectionId($datum['section_id'])
                ->setCustomerGroupIds($datum['valid_customer_group_ids'])
                ->setStoreIds($datum['valid_store_ids'])
                ->setDownloadsLimit((int)$datum['downloads_limit'])
                ->setIsActive($datum['status']);

            $this->eventManager->dispatch(
                'mageworx_downloads_attachment_prepare_save',
                ['attachment' => $attachment]
            );

            $this->attachmentResource->save($attachment);
        }
    }

    /**
     * @param \MageWorx\Downloads\Model\Attachment $attachment
     * @param array $datum
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function uploadFile($attachment, $datum)
    {
        $shortFilePathWithName = $datum['filepath'];
        $fileName              = pathinfo($shortFilePathWithName, PATHINFO_BASENAME);
        $oldFullPath           = $this->fileLinkModel->getImportDir() . DIRECTORY_SEPARATOR . $shortFilePathWithName;

        $data = [
            'multifile' => [
                'name'     => $fileName,
                'tmp_name' => $oldFullPath,
            ],
        ];

        $copiedFileData = $this->copyModel->copyFileAndGetName($this->fileLinkModel->getBaseDir(), $data);

        if (!$attachment->getName()) {
            $attachment->setName($copiedFileData['name']);
        }

        $attachment->setFilename($copiedFileData['file']);
        $attachment->setFiletype(substr($copiedFileData['file'], strrpos($copiedFileData['file'], ' . ') + 1));
        $attachment->setSize(filesize($copiedFileData['path'] . $copiedFileData['file']));
        $attachment->setUrl('');
    }

    /**
     * @param string $name
     * @return \Magento\Framework\DataObject|null
     */
    protected function getSectionIdByName($name)
    {
        if ($this->sectionCollection === null) {
            $this->sectionCollection = $this->sectionCollectionFactory->create();
            $this->sectionCollection->addLocales(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }

        return $this->sectionCollection->getItemByСaseInsensitiveColumnValue('name', $name);
    }

    /**
     * @param array $datum
     * @param string|null $sourceName
     * @return \MageWorx\Downloads\Api\Data\AttachmentLocaleInterface[]
     */
    protected function convertDatumToLocaleObjects(array $datum, ?string $sourceName = null): array
    {
        $storeLabels = [];

        $name = empty($datum['name']) ? $sourceName : $datum['name'];

        $defaultStoreLabelObj = $this->attachmentLocaleFactory->create();
        $defaultStoreLabelObj->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $defaultStoreLabelObj->setStoreName($name);
        $defaultStoreLabelObj->setStoreDescription($datum['description']);

        $storeLabels[] = $defaultStoreLabelObj;

        if ($this->storeLocaleColumnsData) {
            foreach ($this->storeLocaleColumnsData as $specificData) {
                $name        = '';
                $description = '';

                if (isset($specificData[self::NAME_KEY])) {
                    $nameKey = $specificData[self::NAME_KEY];
                    $name    = $datum[$nameKey];
                }

                if (isset($specificData[self::DESCRIPTION_KEY])) {
                    $descriptionKey = $specificData[self::DESCRIPTION_KEY];
                    $description    = $datum[$descriptionKey];
                }

                if ($name || $description) {
                    $storeLabelObj = $this->attachmentLocaleFactory->create();
                    $storeLabelObj->setStoreId((int)$specificData['store_id']);
                    $storeLabelObj->setStoreName($name);
                    $storeLabelObj->setStoreDescription($description);

                    $storeLabels[] = $storeLabelObj;
                }
            }
        }

        return $storeLabels;
    }

    /**
     * @param array $columnNames
     */
    protected function prepareStoreLocaleColumnsData(array $columnNames): void
    {
        $this->storeLocaleColumnsData = [];
        $columnKeys                   = array_flip($columnNames);

        foreach ($this->dataProvider->getStoreCodes() as $storeId => $storeCode) {
            $data = [];

            if (isset($columnKeys[$storeCode . '_name'])) {
                $data[self::NAME_KEY] = $columnKeys[$storeCode . '_name'];
            }

            if (isset($columnKeys[$storeCode . '_description'])) {
                $data[self::DESCRIPTION_KEY] = $columnKeys[$storeCode . '_description'];
            }

            if ($data) {
                $data['store_id']               = $storeId;
                $this->storeLocaleColumnsData[] = $data;
            }
        }
    }

    /**
     * @param array $validData
     * @param int|string $rowIndex
     * @param array $originRowData
     */
    protected function addStoreLocaleDataToValidRowData(array &$validData, $rowIndex, array $originRowData): void
    {
        if ($this->storeLocaleColumnsData) {
            foreach ($this->storeLocaleColumnsData as $datum) {
                if (isset($datum[self::NAME_KEY])) {
                    $nameKey = $datum[self::NAME_KEY];

                    $validData[$rowIndex][$nameKey] = $originRowData[$nameKey];
                }

                if (isset($datum[self::DESCRIPTION_KEY])) {
                    $descriptionKey = $datum[self::DESCRIPTION_KEY];

                    $validData[$rowIndex][$descriptionKey] = $originRowData[$descriptionKey];
                }
            }
        }
    }

    /**
     * @param AttachmentCollection $collection
     * @param array $datum
     * @return \MageWorx\Downloads\Model\Attachment
     */
    protected function getUrlAttachment(
        AttachmentCollection $collection,
        array $datum
    ): \MageWorx\Downloads\Model\Attachment {
        $attachment = $collection->getItemByCaseInsensitiveColumnValue(AttachmentInterface::URL, $datum['url']);

        if (!$attachment) {
            /** @var \MageWorx\Downloads\Model\Attachment $attachment */
            $attachment = $this->attachmentFactory->create();
            $attachment->setType(\MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_URL);
            $attachment->setUrl($datum['url']);
        }

        $attachment->setStoreLocales($this->convertDatumToLocaleObjects($datum, $datum['url']));

        return $attachment;
    }

    /**
     * @param AttachmentCollection $collection
     * @param array $datum
     * @return \MageWorx\Downloads\Model\Attachment|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getFileAttachment(
        AttachmentCollection $collection,
        array $datum
    ): ?\MageWorx\Downloads\Model\Attachment {
        $attachment = $collection->getItemByCaseInsensitiveColumnValue(
            AttachmentInterface::FILENAME,
            $datum['filepath']
        );

        if (!$attachment) {
            $fullPath = $this->fileLinkModel->getImportDir() . DIRECTORY_SEPARATOR . $datum['filepath'];

            if (in_array($fullPath, $this->missedFileNames)) {
                return null;
            }

            /** @var \MageWorx\Downloads\Model\Attachment $attachment */
            $attachment = $this->attachmentFactory->create();
            $attachment->setType(\MageWorx\Downloads\Model\Attachment\Source\ContentType::CONTENT_FILE);

            $this->uploadFile($attachment, $datum);
        }

        $attachment->setStoreLocales($this->convertDatumToLocaleObjects($datum, $attachment->getFilename()));

        return $attachment;
    }

    /**
     * @param array $data
     * @return AttachmentCollection
     */
    protected function getAttachmentCollection(array $data): AttachmentCollection
    {
        $fileNames = array_filter(array_unique(array_column($data, 'filepath')));
        $urls      = array_filter(array_unique(array_column($data, 'url')));

        /** @var AttachmentCollection $collection */
        $collection = $this->attachmentCollectionFactory->create();
        $select     = $collection->getSelect();
        $cond       = [];

        if ($fileNames) {
            $cond[] = $collection->getConnection()->quoteInto(
                'main_table.' . AttachmentInterface::FILENAME . ' IN (?)',
                $fileNames
            );
        }

        if ($urls) {
            $cond[] = $collection->getConnection()->quoteInto(
                'main_table.' . AttachmentInterface::URL . ' IN (?)',
                $urls
            );
        }
        $cond = implode(' OR ', $cond);
        $select->where($cond);

        return $collection;
    }
}
