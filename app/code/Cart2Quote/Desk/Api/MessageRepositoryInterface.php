<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Api;

/**
 * Interface MessageRepositoryInterface
 * @package Cart2Quote\Desk\Api
 */
interface MessageRepositoryInterface
{
    /**
     * Create message.
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @api
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface
     */
    public function save(\Cart2Quote\Desk\Api\Data\MessageInterface $message);

    /**
     * Retrieve message.
     *
     * @param int $messageId
     * @api
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($messageId);

    /**
     * Retrieve message which match a specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @api
     *
     * @return []
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete message.
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Cart2Quote\Desk\Api\Data\MessageInterface $message);

    /**
     * Delete message by ID.
     *
     * @param int $messageId
     * @api
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($messageId);
}
