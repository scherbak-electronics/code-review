<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Downloads\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for attachment search results.
 *
 * @api
 */
interface AttachmentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \MageWorx\Downloads\Api\Data\AttachmentInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \MageWorx\Downloads\Api\Data\AttachmentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
