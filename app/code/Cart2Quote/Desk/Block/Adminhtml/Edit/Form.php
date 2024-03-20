<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Edit;

/**
 * Class Form
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit
 */
class Form extends \Magento\Backend\Block\Template
{
    /**
     * Ticket
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $ticket = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Class Form constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get the ticket from registry
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getTicket()
    {
        if (!$this->ticket) {
            $this->ticket = $this->coreRegistry->registry('ticket_data');
        }

        return $this->ticket;
    }

    /**
     * Get the ticket id
     *
     * @return int
     */
    public function getTicketId()
    {
        if ($this->getTicket()) {
            return $this->getParentBlock()->getTicket()->getId();
        } else {
            return 0;
        }
    }
}
