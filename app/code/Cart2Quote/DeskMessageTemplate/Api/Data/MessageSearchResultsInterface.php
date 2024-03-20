<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MessageSearchResultsInterface
 * @package Cart2Quote\DeskMessageTemplate\Api\Data
 */
interface MessageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Cart2Quote\DeskMessageTemplate\Api\Data\MessageInterface[]
     */
    public function getItems();

    /**
     * @param \Cart2Quote\DeskMessageTemplate\Api\Data\MessageInterface[] $items
     * @return MessageSearchResultsInterface
     */
    public function setItems(array $items);
}