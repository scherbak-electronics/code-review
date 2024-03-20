<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Container\Messages;

/**
 * Class Message
 * @package Cart2Quote\Desk\Block\Adminhtml\Edit\Container\Messages
 */
class Message extends \Magento\Backend\Block\Template
{

    /**
     * Get the message name
     *
     * @return string
     */
    public function getName()
    {
        return $this->escapeHtml($this->getMessage()->getName());
    }

    /**
     * Get the message updated at value
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getMessage()->getUpdatedAt();
    }

    /**
     * Get the message content value
     *
     * @return string
     */
    public function getContent()
    {
        return nl2br($this->escapeHtml($this->getMessage()->getMessage()));
    }

    /**
     * Get the CSS classes for a single message container
     *
     * @return string
     */
    public function getMessageClasses()
    {
        return $this->getOwnerClass() . ' ' . $this->getIsPrivateClass() . ' ' . $this->getIsNewClass();
    }

    /**
     * Get the for the owner: customer or admin user.
     *
     * @return string
     */
    public function getOwnerClass()
    {
        $class = 'customer-message';
        if ($this->getMessage()->getUserId()) {
            $class = 'admin-message';
        }

        return $class;
    }

    /**
     * Get the class for an internal note.
     *
     * @return string
     */
    public function getIsPrivateClass()
    {
        $class = '';
        if ($this->getMessage()->getIsPrivate()) {
            $class = 'internal-note';
        }

        return $class;
    }

    /**
     * Get the new css class by the is_new data variable
     *
     * @return string
     */
    public function getIsNewClass()
    {
        $class = '';
        if ($this->getIsNew()) {
            $class = 'new-message';
        }

        return $class;
    }

    /**
     * Get the NEW notice for a new ticket.
     *
     * @return string
     */
    public function getNewHtml()
    {
        $html = '';
        if ($this->getIsNew()) {
            $html = '<span class="new-message-notice">' . __("NEW") . '</span>';
        }

        return $html;
    }
}
