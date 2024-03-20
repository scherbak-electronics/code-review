<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\Downloads\Api;

use MageWorx\Downloads\Api\Data\AttachmentInterface;

/**
 * Interface AttachmentRepositoryInterface
 *
 * @api
 */
interface AttachmentRepositoryInterface
{
    /**
     * Search Attachments
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageWorx\Downloads\Api\Data\AttachmentSearchResultsInterface containing Data\AttachmentInterface objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve page.
     *
     * @param int $attachmentId
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $attachmentId): \MageWorx\Downloads\Api\Data\AttachmentInterface;

    /**
     * Update attachment of the given product
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentInterface $attachment
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface
     */
    public function save(AttachmentInterface $attachment): AttachmentInterface;

    /**
     * Delete attachment
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id): bool;
}
