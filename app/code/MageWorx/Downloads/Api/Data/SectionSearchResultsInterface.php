<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for section search results.
 *
 * @api
 */
interface SectionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \MageWorx\Downloads\Api\Data\SectionInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \MageWorx\Downloads\Api\Data\SectionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
