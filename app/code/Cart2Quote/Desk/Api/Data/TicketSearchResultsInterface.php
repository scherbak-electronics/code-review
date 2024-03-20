<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Api\Data;

/**
 * Interface TicketSearchResultsInterface
 * @package Cart2Quote\Desk\Api\Data
 */
interface TicketSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get ticket list.
     *
     * @api
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface[]
     */
    public function getItems();

    /**
     * Set ticket list.
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface[] $items
     * @api
     *
     * @return $this
     */
    public function setItems(array $items);
}
