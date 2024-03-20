<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\Config\Source;

use Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority\Collection;
use Magento\Framework\Option\ArrayInterface;
use \Cart2Quote\Desk\Helper\ResourceModel\Collection as Helper;

/**
 * Class Priority
 * @package Cart2Quote\Desk\Model\Config\Source
 */
class Priority implements ArrayInterface
{
    /**
     * Priority Collection
     *
     * @var Collection
     */
    protected $collection;

    /**
     * Cart2Quote Data Helper
     *
     * @var Helper
     */
    protected $helper;

    /**
     * Class Priority constructor
     *
     * @param Collection $collection
     * @param Helper $helper
     */
    public function __construct(
        Collection $collection,
        Helper $helper
    ) {
        $this->collection = $collection;
        $this->helper = $helper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->helper->ucfirstToOptionArray($this->collection);
    }
}
