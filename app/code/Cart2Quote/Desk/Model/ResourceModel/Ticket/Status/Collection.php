<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket\Status;

/**
 * Class Collection
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket\Status
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Cart2Quote Collection Helper
     *
     * @var \Cart2Quote\Desk\Helper\ResourceModel\Collection
     */
    protected $collectionHelper = null;

    /**
     * Ticket Status collection constructor
     *
     * @param \Cart2Quote\Desk\Helper\ResourceModel\Collection $collectionHelper
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\ResourceModel\Collection $collectionHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->collectionHelper = $collectionHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\Ticket\Status', 'Cart2Quote\Desk\Model\ResourceModel\Ticket\Status');
    }

    /**
     * Get the option array by status id and code
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('status_id', 'code');
    }

    /**
     * Sets array(label => data, value => data)
     * to array(-label- data, -value- data)
     *
     * @return array
     */
    public function toGridOptionArray()
    {
        return $this->collectionHelper->toGridOptionArray($this);
    }
}
