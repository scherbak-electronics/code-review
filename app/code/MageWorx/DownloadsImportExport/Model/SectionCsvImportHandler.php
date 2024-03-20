<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DownloadsImportExport\Model;

use MageWorx\Downloads\Model\ResourceModel\Section;
use MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory;
use MageWorx\Downloads\Model\Section\Source\IsActive;
use MageWorx\DownloadsImportExport\Helper\DataProvider;

class SectionCsvImportHandler
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
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var IsActive
     */
    protected $sectionStatusOptions;

    /**
     * @var CollectionFactory
     */
    protected $sectionCollectionFactory;

    /**
     * @var \MageWorx\Downloads\Model\ResourceModel\Section\Collection
     */
    protected $sectionCollection;

    /**
     * @var \MageWorx\Downloads\Model\SectionRepository
     */
    protected $sectionRepository;

    /**
     * @var \MageWorx\Downloads\Model\SectionFactory
     */
    protected $sectionFactory;

    /**
     * @var \MageWorx\Downloads\Api\Data\SectionLocaleInterfaceFactory
     */
    protected $sectionLocaleFactory;

    /**
     * @var Section
     */
    protected $sectionResource;

    /**
     * @var array
     */
    protected $storeLocaleColumnsData = [];

    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * SectionCsvImportHandler constructor.
     *
     * @param IsActive $sectionStatusOptions
     * @param CollectionFactory $sectionCollectionFactory
     * @param Section $sectionResource
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \MageWorx\Downloads\Model\SectionRepository $sectionRepository
     * @param \MageWorx\Downloads\Model\SectionFactory $sectionFactory
     * @param \MageWorx\Downloads\Api\Data\SectionLocaleInterfaceFactory $sectionLocaleFactory
     * @param DataProvider $dataProvider
     */
    public function __construct(
        \MageWorx\Downloads\Model\Section\Source\IsActive $sectionStatusOptions,
        \MageWorx\Downloads\Model\ResourceModel\Section\CollectionFactory $sectionCollectionFactory,
        \MageWorx\Downloads\Model\ResourceModel\Section $sectionResource,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \MageWorx\Downloads\Model\SectionRepository $sectionRepository,
        \MageWorx\Downloads\Model\SectionFactory $sectionFactory,
        \MageWorx\Downloads\Api\Data\SectionLocaleInterfaceFactory $sectionLocaleFactory,
        DataProvider $dataProvider
    ) {
        $this->sectionStatusOptions     = $sectionStatusOptions;
        $this->sectionCollectionFactory = $sectionCollectionFactory;
        $this->sectionResource          = $sectionResource;
        $this->escaper                  = $escaper;
        $this->csvProcessor             = $csvProcessor;
        $this->dataObjectFactory        = $dataObjectFactory;
        $this->sectionRepository        = $sectionRepository;
        $this->sectionFactory           = $sectionFactory;
        $this->sectionLocaleFactory     = $sectionLocaleFactory;
        $this->dataProvider             = $dataProvider;
    }

    /**
     * @param array $file file info retrieved from $_FILES array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $data = $this->csvProcessor->getData($file['tmp_name']);

        if (count($data) < 3) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Data for import not found.')
            );
        }

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
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getValidatedData(array $data)
    {
        $uniqueArray = [];

        foreach ($data as $rowIndex => $dataRow) {

            if (!array_key_exists(0, $dataRow) || $dataRow[0] === '') {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Missed section name (title) in line %1', $rowIndex + 2)
                );
            }

            $statusOptions = array_map(
                'strval',
                array_column($this->sectionStatusOptions->toOptionArray(), 'value')
            );

            if (!array_key_exists(2, $dataRow) || !in_array($dataRow[2], $statusOptions)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid status value in line %1', $rowIndex + 2)
                );
            }

            $uniqueKey = strtolower($dataRow[0]);

            if (\array_key_exists($uniqueKey, $uniqueArray)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("Duplicate row (same section's name) was found in line %1", $rowIndex + 2)
                );
            }

            $uniqueArray[$uniqueKey] = [
                'name'        => $dataRow[0],
                'description' => $dataRow[1],
                'status'      => $dataRow[2],
            ];

            $this->addStoreLocaleDataToValidRowData($uniqueArray, $uniqueKey, $dataRow);
        }

        return $uniqueArray;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    protected function import(array $data)
    {
        if ($data) {
            $this->sectionCollection = $this->sectionCollectionFactory->create();
            $this->sectionCollection->addLocales(\Magento\Store\Model\Store::DEFAULT_STORE_ID)->load();

            foreach ($data as $uniqKey => $datum) {

                /** @var \MageWorx\Downloads\Model\Section $presentSection */
                $presentSection = $this->sectionCollection->getItemByСaseInsensitiveColumnValue('name', $datum['name']);

                if ($presentSection) {
                    $presentSection->setStoreLocales($this->convertDatumToLocaleObjects($datum));
                    $presentSection->setIsActive($datum['status']);
                    $this->sectionResource->save($presentSection);
                    continue;
                }

                $section = $this->sectionFactory->create();
                $section->setStoreLocales($this->convertDatumToLocaleObjects($datum));
                $section->setIsActive($datum['status']);

                $this->sectionRepository->save($section);
            }
        }
    }

    /**
     * @param array $datum
     * @return \MageWorx\Downloads\Api\Data\SectionLocaleInterface[]
     */
    protected function convertDatumToLocaleObjects(array $datum): array
    {
        $storeLabels = [];

        /** @var \MageWorx\Downloads\Api\Data\SectionLocaleInterface $defaultStoreLabelObj */
        $defaultStoreLabelObj = $this->sectionLocaleFactory->create();
        $defaultStoreLabelObj->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $defaultStoreLabelObj->setStoreName($datum['name']);
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
                    /** @var \MageWorx\Downloads\Api\Data\SectionLocaleInterface $storeLabelObj */
                    $storeLabelObj = $this->sectionLocaleFactory->create();
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
}
