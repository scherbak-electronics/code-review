<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @package Cart2Quote_Desk
 * @author Cart2Quote
 * @author Lennart van der Garde <lennart@cart2quote.com>
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

use Cart2Quote\Desk\Model\ResourceModel\Ticket\Priority as PriorityResourceModel;
use Cart2Quote\Desk\Model\Ticket\PriorityFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PriorityRepository
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket
 */
class PriorityRepository
{
    /** @var PriorityResourceModel $resource */
    protected $resource;

    /** @var PriorityFactory $priorityFactory */
    protected $priorityFactory;

    /**
     * PriorityRepository constructor.
     * @param PriorityFactory $priorityFactory
     * @param Priority $priorityResourceModel
     */
    public function __construct(
        PriorityFactory $priorityFactory,
        PriorityResourceModel $priorityResourceModel
    )
    {
        $this->resource = $priorityResourceModel;
        $this->priorityFactory = $priorityFactory;
    }

    /**
     * @param $id
     * @return \Cart2Quote\Desk\Model\Ticket\Priority
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $priority = $this->priorityFactory->create();
        $this->resource->load($priority, $id);
        if (! $priority->getId()) {
            throw new NoSuchEntityException(__('Unable to find priority with ID "%1"', $id));
        }

        return $priority;
    }
}