<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Container;

/**
 * Class Create
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit\Container
 */
class Create extends \Magento\Backend\Block\Template
{
    /**
     * Get the ticket from parent block
     *
     * @return \Cart2Quote\Desk\Model\Ticket
     */
    public function getTicket()
    {
        /**
         * Parent set in layout.xml
         * @var \Cart2Quote\Desk\Block\Adminhtml\Edit\Form $parentBlock
         */
        $parentBlock = $this->getParentBlock();
        return $parentBlock->getTicket();
    }

    /**
     * Get the subject
     *
     * @return String
     */
    public function getSubject()
    {
        $subject = '';
        if ($this->getTicket()) {
            $subject = $this->getTicket()->getSubject();
        }

        return $subject;
    }

    /**
     * Get the status
     *
     * @return String
     */
    public function getStatus()
    {
        $status = __(\Cart2Quote\Desk\Model\Ticket\Status::STATUS_OPEN);
        if ($this->getTicket()) {
            $status = $this->getTicket()->getStatus();
        }

        return $status;
    }
}
