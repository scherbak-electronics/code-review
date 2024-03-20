<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\Downloads\Model;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\Downloads\Api\Data\AttachmentLinkInterface;
use MageWorx\Downloads\Model\ResourceModel\Attachment\Collection as AttachmentCollection;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Framework\Event\ManagerInterface as EventManager;

class AttachmentManager implements \MageWorx\Downloads\Api\AttachmentManagerInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductStatus
     */
    protected $productStatus;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var \MageWorx\Downloads\Api\Data\AttachmentInterfaceFactory
     */
    protected $attachmentDataObjectFactory;
    /**
     * @var AttachmentLinkBuilder
     */
    protected $attachmentLinkBuilder;


    public function __construct(
        \MageWorx\Downloads\Api\Data\AttachmentInterfaceFactory $attachmentDataObjectFactory,
        \MageWorx\Downloads\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\Downloads\Model\AttachmentLinkBuilder $attachmentLinkBuilder,
        EventManager $eventManager
    ) {
        $this->customerRepository          = $customerRepository;
        $this->storeManager                = $storeManager;
        $this->eventManager                = $eventManager;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->attachmentDataObjectFactory = $attachmentDataObjectFactory;
        $this->attachmentLinkBuilder       = $attachmentLinkBuilder;
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return array|AttachmentLinkInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByProductId(int $customerId, int $productId): array
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return [];
        }

        return $this->findByProductId($productId, $customer);
    }

    /**
     * Retrieve attachment links filtered by product ID for frontend using
     *
     * @param int $productId
     * @param \Magento\Customer\Api\Data\CustomerInterface|null $customer
     *
     * @return AttachmentLinkInterface[]
     */
    public function findByProductId(int $productId, $customer = null): array
    {
        $attachmentList  = [];
        $customerGroupId = $customer ? (int)$customer->getGroupId() : GroupInterface::NOT_LOGGED_IN_ID;

        $attachments = $this->getAttachments($customerGroupId, $productId);

        /** @var \MageWorx\Downloads\Api\Data\AttachmentInterface $resourceAttachment */
        foreach ($attachments as $resourceAttachment) {
            $attachmentList[] = $this->attachmentLinkBuilder->build($resourceAttachment);
        }

        return $attachmentList;
    }

    /**
     * @param int|null $customerId
     * @param array $attachmentIds
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByAttachmentIds(?int $customerId = null, array $attachmentIds = []) : array
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return [];
        }

        return $this->findByAttachmentIds($attachmentIds, $customer);
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return array
     * @throws NoSuchEntityException
     */
    protected function findByAttachmentIds(array $attachmentIds, $customer = null): array
    {
        $attachmentList  = [];
        $customerGroupId = $customer ? (int)$customer->getGroupId() : GroupInterface::NOT_LOGGED_IN_ID;

        $attachments = $this->getAttachments($customerGroupId, null, $attachmentIds);

        /** @var \MageWorx\Downloads\Api\Data\AttachmentInterface $resourceAttachment */
        foreach ($attachments as $resourceAttachment) {
            $attachmentList[] = $this->attachmentLinkBuilder->build($resourceAttachment);
        }

        return $attachmentList;
    }

    /**
     * @param int|null $customerGroupId
     * @param int|null $productId
     * @param array $attachmentIds
     * @param array $sectionIds
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAttachments(
        ?int $customerGroupId = null,
        ?int $productId = null,
        array $attachmentIds = [],
        array $sectionIds = []
    ): array {
        /** @var AttachmentCollection $attachmentCollection */
        $attachmentCollection = $this->attachmentCollectionFactory->create();

        $attachmentCollection->addStoreFilter($this->storeManager->getStore()->getId())
                             ->addEnabledFilter()
                             ->addSectionEnabledFilter()
                             ->addLocales()
                             ->addSectionLocales()
                             ->addDownloadsLimitFilter();

        if ($customerGroupId !== null) {
            $attachmentCollection->addCustomerGroupFilter($customerGroupId);
        }

        if ($productId) {
            $attachmentCollection->addProductFilter($productId);
        }

        if ($attachmentIds) {
            $attachmentCollection->addAttachmentsFilter($attachmentIds);
        }

        if ($sectionIds) {
            $attachmentCollection->addSectionsFilter($sectionIds);
        }

        $attachmentCollection->addSortOrder();

        $attachments = [];
        foreach ($attachmentCollection as $attachment) {
            /* @var \MageWorx\Downloads\Api\Data\AttachmentInterface $attachment */
            $attachments[$attachment->getId()] = $attachment;
        }

        return $attachments;
    }
}
