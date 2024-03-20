<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Api;

use Cart2Quote\DeskMessageTemplate\Api\Data\MessageSearchResultsInterface;
use Cart2Quote\DeskMessageTemplate\Api\Data\MessageInterface;

/**
 * Interface MessageRepositoryInterface
 * @package Cart2Quote\DeskMessageTemplate\Api
 */
interface MessageRepositoryInterface
{
    /**
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function save(MessageInterface $message);

    /**
     * @param string $messageId
     * @return MessageInterface
     */
    public function getById($messageId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return MessageSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function delete(MessageInterface $message);

    /**
     * @param $messageId
     * @return bool
     */
    public function deleteById($messageId);
}