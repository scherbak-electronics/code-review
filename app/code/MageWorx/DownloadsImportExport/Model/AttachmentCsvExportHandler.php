<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\DownloadsImportExport\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\Store;
use MageWorx\Downloads\Api\AttachmentRepositoryInterface;
use MageWorx\Downloads\Api\Data\AttachmentInterface;
use MageWorx\DownloadsImportExport\Helper\DataProvider;
use MageWorx\Downloads\Api\Data\AttachmentLocaleInterface;

class AttachmentCsvExportHandler
{
    const KEY_SECTION_NAME    = 'section_name';
    const KEY_PRODUCTS        = 'products';
    const KEY_CUSTOMER_GROUPS = 'customer_groups';
    const KEY_STORES          = 'stores';

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

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
     * AttachmentCsvExportHandler constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AttachmentRepositoryInterface $attachmentRepository
     * @param CsvWriter $csvWriter
     * @param DataProvider $dataProvider
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AttachmentRepositoryInterface $attachmentRepository,
        CsvWriter $csvWriter,
        DataProvider $dataProvider
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->attachmentRepository  = $attachmentRepository;
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
        $attachments          = $this->getAttachments($ids);

        $this->prepareAttachments($attachments);

        $headers    = $this->getHeaders();
        $headerKeys = array_keys($headers);
        $content[]  = array_values($headers);

        /** @var AttachmentInterface|\MageWorx\Downloads\Model\Attachment $attachment */
        foreach ($attachments as $attachment) {
            $attachmentData = [];
            foreach ($headerKeys as $key) {
                $attachmentData[] = (string)$attachment->getData($key);
            }
            $content[] = $attachmentData;
        }

        return $this->csvWriter->write($content);
    }

    /**
     * @param array $attachments
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function prepareAttachments(array $attachments): void
    {
        /** @var AttachmentInterface|\MageWorx\Downloads\Model\Attachment $attachment */
        foreach ($attachments as $attachment) {
            $attachment->addData(
                [
                    self::KEY_SECTION_NAME           => $this->dataProvider->getSectionName(
                        (int)$attachment->getSectionId()
                    ),
                    AttachmentInterface::NAME        => $attachment->getName(Store::DEFAULT_STORE_ID),
                    AttachmentInterface::DESCRIPTION => $attachment->getDescription(Store::DEFAULT_STORE_ID),
                    self::KEY_PRODUCTS               => $this->dataProvider->getProductsAsString(
                        (array)$attachment->getProductIds()
                    ),
                    self::KEY_CUSTOMER_GROUPS        => $this->dataProvider->getCustomerGroupsAsString(
                        (array)$attachment->getCustomerGroupIds()
                    ),
                    self::KEY_STORES                 => $this->dataProvider->getStoresAsString(
                        (array)$attachment->getStoreIds()
                    )
                ]
            );

            $storeLocales = $attachment->getStoreLocales();

            if ($storeLocales) {
                /** @var AttachmentLocaleInterface $attachmentLocale */
                foreach ($storeLocales as $attachmentLocale) {
                    $storeId = $attachmentLocale->getStoreId();

                    if ($storeId == Store::DEFAULT_STORE_ID) {
                        continue;
                    }

                    $storeCodes = $this->dataProvider->getStoreCodes();

                    if (!isset($storeCodes[$storeId])) {
                        continue;
                    }

                    $storeCode                        = $storeCodes[$storeId];
                    $this->usedStoreCodes[$storeCode] = 1;

                    $attachment->addData(
                        [
                            $storeCode . '_name'        => $attachmentLocale->getStoreName(),
                            $storeCode . '_description' => $attachmentLocale->getStoreDescription()
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
            self::KEY_SECTION_NAME               => __('Section'),
            AttachmentInterface::NAME            => __('Name'),
            AttachmentInterface::FILENAME        => __('File'),
            AttachmentInterface::URL             => __('URL'),
            AttachmentInterface::DESCRIPTION     => __('Description'),
            self::KEY_PRODUCTS                   => __('Products'),
            self::KEY_CUSTOMER_GROUPS            => __('Customer Groups'),
            self::KEY_STORES                     => __('Stores'),
            AttachmentInterface::DOWNLOADS_LIMIT => __('Downloads Limit'),
            AttachmentInterface::IS_ACTIVE       => __('Is Active')
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
    protected function getAttachments(array $ids): array
    {
        if ($ids) {
            $this->searchCriteriaBuilder->addFilter(AttachmentInterface::ID, $ids, 'in');
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result         = $this->attachmentRepository->getList($searchCriteria);

        return $result->getItems();
    }
}
