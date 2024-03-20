<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\DownloadsImportExport\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Api\Data\SectionInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use MageWorx\Downloads\Api\SectionRepositoryInterface;
use MageWorx\Downloads\Api\Data\SectionLocaleInterface;
use MageWorx\DownloadsImportExport\Helper\DataProvider;

class SectionCsvExportHandler
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var SectionRepositoryInterface
     */
    protected $sectionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CsvWriter
     */
    protected $csvWriter;

    /**
     * @var array
     */
    protected $usedStoreCodes = [];

    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * SectionCsvExportHandler constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param SectionRepositoryInterface $sectionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CsvWriter $csvWriter
     * @param DataProvider $dataProvider
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        SectionRepositoryInterface $sectionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CsvWriter $csvWriter,
        DataProvider $dataProvider
    ) {
        $this->dataObjectFactory     = $dataObjectFactory;
        $this->sectionRepository     = $sectionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->csvWriter             = $csvWriter;
        $this->dataProvider          = $dataProvider;
    }

    /**
     * @param array $ids
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getContent(array $ids = []): array
    {
        $this->usedStoreCodes = [];
        $sections             = $this->getSections($ids);

        $this->prepareSections($sections);

        $headers    = $this->getHeaders();
        $headerKeys = array_keys($headers);
        $content[]  = array_values($headers);

        foreach ($sections as $section) {
            $sectionData = [];
            foreach ($headerKeys as $key) {
                $sectionData[] = (string)$section->getData($key);
            }
            $content[] = $sectionData;
        }

        return $this->csvWriter->write($content);
    }

    /**
     * @param array $sections
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function prepareSections(array $sections): void
    {
        /** @var \MageWorx\Downloads\Model\Section $section */
        foreach ($sections as $section) {
            $section->addData(
                [
                    SectionInterface::NAME        => $section->getName(Store::DEFAULT_STORE_ID),
                    SectionInterface::DESCRIPTION => $section->getDescription(Store::DEFAULT_STORE_ID)
                ]
            );

            $storeLocales = $section->getStoreLocales();

            if ($storeLocales) {
                /** @var SectionLocaleInterface $sectionLocale */
                foreach ($storeLocales as $sectionLocale) {
                    $storeId = $sectionLocale->getStoreId();

                    if ($storeId == Store::DEFAULT_STORE_ID) {
                        continue;
                    }

                    $storeCodes = $this->dataProvider->getStoreCodes();

                    if (!isset($storeCodes[$storeId])) {
                        continue;
                    }

                    $storeCode                        = $storeCodes[$storeId];
                    $this->usedStoreCodes[$storeCode] = 1;

                    $section->addData(
                        [
                            $storeCode . '_name'        => $sectionLocale->getStoreName(),
                            $storeCode . '_description' => $sectionLocale->getStoreDescription()
                        ]
                    );
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        $dataFields = [
            SectionInterface::NAME        => __('Name'),
            SectionInterface::DESCRIPTION => __('Description'),
            SectionInterface::IS_ACTIVE   => __('Is Active'),
        ];

        if ($this->usedStoreCodes) {
            foreach (array_keys($this->usedStoreCodes) as $storeCode) {
                $dataFields[$storeCode . '_name']        = $storeCode . '_name';
                $dataFields[$storeCode . '_description'] = $storeCode . '_description';
            }
        }

        return $dataFields;
    }

    /**
     * @param array $ids
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getSections(array $ids): array
    {
        if ($ids) {
            $this->searchCriteriaBuilder->addFilter(SectionInterface::ID, $ids, 'in');
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result         = $this->sectionRepository->getList($searchCriteria);

        return $result->getItems();
    }
}
