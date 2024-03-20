<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api\Data;

/**
 * Interface UserSearchResultsInterface
 * @package Cart2Quote\SalesRep\Api\Data
 */
interface UserSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get User list.
     *
     * @api
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface[]
     */
    public function getItems();

    /**
     * Set User list.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterface[] $items
     * @api
     *
     * @return $this
     */
    public function setItems(array $items);
}
