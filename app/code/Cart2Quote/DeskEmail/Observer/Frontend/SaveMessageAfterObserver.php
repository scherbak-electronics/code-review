<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskEmail\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveMessageAfterObserver
 * @package Cart2Quote\DeskEmail\Observer\Frontend
 */
class SaveMessageAfterObserver implements ObserverInterface
{
    /**
     * Message Sender
     *
     * @var \Cart2Quote\DeskEmail\Model\Sender\MessageSender
     */
    protected $_messageSender;

    /**
     * Class SaveMessageAfterObserver constructor
     *
     * @param \Cart2Quote\DeskEmail\Model\Sender\MessageSender $messageSender
     */
    public function __construct(
        \Cart2Quote\DeskEmail\Model\Sender\MessageSender $messageSender
    ) {
        $this->_messageSender = $messageSender;
    }

    /**
     * Send message to the message receiver.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $ticket = $observer->getEvent()->getTicket();
        $message = $observer->getEvent()->getMessage();

        $this->_messageSender->send($ticket, $message);

        return $this;
    }
}
