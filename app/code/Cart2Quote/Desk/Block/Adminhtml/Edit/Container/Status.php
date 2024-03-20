<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Container;

/**
 * Class Status
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit\Container
 */
class Status extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Ticket Status constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get ticket status
     *
     * @return string
     */
    public function getStatus()
    {
        if ($this->getTicket()) {
            $status = $this->getTicket()->getStatus();
        } else {
            $status = __(\Cart2Quote\Desk\Model\Ticket\Status::STATUS_OPEN);
        }

        return $status;
    }

    /**
     * Get ticket data
     *
     * @return \Cart2Quote\Desk\Model\Ticket
     */
    public function getTicket()
    {
        return $this->coreRegistry->registry('ticket_data');
    }
}
