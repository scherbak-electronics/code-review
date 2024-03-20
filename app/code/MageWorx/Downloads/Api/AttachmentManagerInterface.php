<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\Downloads\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\Downloads\Api\Data\AttachmentLinkInterface;

interface AttachmentManagerInterface
{
    /**
     * @param int $customerId
     * @param int $productId
     * @return \MageWorx\Downloads\Api\Data\AttachmentLinkInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByProductId(int $customerId, int $productId): array;

    /**
     * @param int|null $customerId
     * @param array $attachmentIds
     * @return \MageWorx\Downloads\Api\Data\AttachmentLinkInterface[]
     * @throws NoSuchEntityException
     */
    public function getByAttachmentIds(?int $customerId = null, array $attachmentIds = []) : array;

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
    ): array;
}
