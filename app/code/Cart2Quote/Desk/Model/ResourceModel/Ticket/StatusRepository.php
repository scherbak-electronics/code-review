<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

use Cart2Quote\Desk\Model\ResourceModel\Ticket\Status as StatusResourceModel;
use Cart2Quote\Desk\Model\Ticket\StatusFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class StatusRepository
 * @package Cart2Quote\Desk\Model\ResourceModel\Ticket
 */
class StatusRepository
{
    /** @var StatusFactory $statusFactory */
    protected $statusFactory;

    /** @var StatusResourceModel $resource */
    protected $resource;

    /**
     * StatusRepository constructor.
     * @param StatusFactory $statusFactory
     * @param Status $statusResourceModel
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceModel $statusResourceModel)
    {
        $this->statusFactory = $statusFactory;
        $this->resource = $statusResourceModel;
    }

    /**
     * @param $id
     * @return \Cart2Quote\Desk\Model\Ticket\Status
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $status = $this->statusFactory->create();
        $this->resource->load($status, $id);
        if (! $status->getId()) {
            throw new NoSuchEntityException(__('Unable to find status with ID "%1"', $id));
        }
        return $status;
    }
}