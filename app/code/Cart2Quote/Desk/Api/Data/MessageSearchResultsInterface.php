<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Api\Data;

/**
 * Interface MessageSearchResultsInterface
 * @package Cart2Quote\Desk\Api\Data
 */
interface MessageSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get message list.
     *
     * @api
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface[]
     */
    public function getItems();

    /**
     * Set message list.
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface[] $items
     * @api
     *
     * @return $this
     */
    public function setItems(array $items);
}
