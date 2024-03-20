<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api\Data;

/**
 * Interface TypeSearchResultsInterface
 * @package Cart2Quote\SalesRep\Api\Data
 */
interface TypeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Type list.
     *
     * @api
     * @return \Cart2Quote\SalesRep\Api\Data\TypeInterface[]
     */
    public function getItems();

    /**
     * Set Type list.
     *
     * @param \Cart2Quote\SalesRep\Api\Data\TypeInterface[] $items
     * @api
     *
     * @return $this
     */
    public function setItems(array $items);
}
