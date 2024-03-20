<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskEmail\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class NewTicketObserver
 * @package Cart2Quote\DeskEmail\Observer\Frontend
 */
class NewTicketObserver implements ObserverInterface
{
    /**
     * New Ticket Sender
     *
     * @var \Cart2Quote\DeskEmail\Model\Sender\newTicketSender
     */
    protected $_newTicketSender;

    /**
     * Desk Helper
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * Class NewTicketObserver constructor
     *
     * @param \Cart2Quote\DeskEmail\Model\Sender\NewTicketSender $newTicketSender
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(
        \Cart2Quote\DeskEmail\Model\Sender\NewTicketSender $newTicketSender,
        \Magento\Backend\Helper\Data $helper
    ) {
        $this->_newTicketSender = $newTicketSender;
        $this->_helper = $helper;
    }

    /**
     * Send new ticket email to the message receiver.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $ticket = $observer->getEvent()->getTicket();
        $message = $observer->getEvent()->getMessage();
        $this->_newTicketSender->send($ticket, $message);

        return $this;
    }
}
