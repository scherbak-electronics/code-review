<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab\Container;

/**
 * Class Messages
 * @package Cart2Quote\Desk\Block\Adminhtml\Quote\View\Tab\Container
 */
class Messages extends \Cart2Quote\Desk\Block\Adminhtml\Edit\Container\Messages
{
    /**
     * Get the ticket ID
     *
     * @return int
     */
    public function getTicketId()
    {
        $ticketId = false;
        if ($this->getTicket()) {
            $ticketId = $this->getParentBlock()->getTicketId();
        }

        return $ticketId;
    }

    /**
     * Get the AJAX update message URL
     *
     * @return string
     */
    public function getAjaxUpdateMessagesUrl()
    {
        $url =  $this->getUrl(
            'desk/ticket/listmessage/',
            [
                'id' => $this->getTicketId()
            ]
        );

        return $url;
    }

    /**
     * Get the quote
     *
     * @return \Cart2Quote\Desk\Model\Ticket
     */
    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }
}
