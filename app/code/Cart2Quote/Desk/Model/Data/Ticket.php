<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\Data;

/**
 * Class Ticket
 * @package Cart2Quote\Desk\Model\Data
 */
class Ticket extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Cart2Quote\Desk\Api\Data\TicketInterface
{
    /**
     * _get ticket id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get created at time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Get updated at time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Get status id
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->_get(self::STATUS_ID);
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Get priority id
     *
     * @return int
     */
    public function getPriorityId()
    {
        return $this->_get(self::PRIORITY_ID);
    }

    /**
     * Get priority id
     *
     * @return string|null
     */
    public function getPriority()
    {
        return $this->_get(self::PRIORITY);
    }

    /**
     * Get assignee id
     *
     * @return int
     */
    public function getAssigneeId()
    {
        return $this->_get(self::ASSIGNEE_ID);
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set ticket id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set updated at time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Set status id
     *
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId)
    {
        return $this->setData(self::STATUS_ID, $statusId);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set customer id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set priority id
     *
     * @param string $priorityId
     * @return $this
     */
    public function setPriorityId($priorityId)
    {
        return $this->setData(self::PRIORITY_ID, $priorityId);
    }

    /**
     * Set priority
     *
     * @param string $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Set assignee id
     *
     * @param string $assigneeId
     * @return string
     */
    public function setAssigneeId($assigneeId)
    {
        return $this->setData(self::ASSIGNEE_ID, $assigneeId);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get updated at time
     *
     * @api
     * @return string|null
     */
    public function getSubject()
    {
        return $this->_get(self::SUBJECT);
    }

    /**
     * Set updated at time
     *
     * @param string $subject
     * @api
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * Get customer name
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->_get(self::CUSTOMER_NAME);
    }

    /**
     * Set customer name
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @param string $customerName
     * @api
     *
     * @return $this
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Get customer email
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->_get(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer email
     *
     * Notice:
     * This data is loaded from the customer_entity table and cannot be saved via the message.
     *
     * @param string $customerEmail
     * @api
     *
     * @return $this
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Get assignee name
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getAssigneeName()
    {
        return $this->_get(self::ASSIGNEE_NAME);
    }

    /**
     * Set assignee name
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @param string $assigneeName
     * @api
     *
     * @return $this
     */
    public function setAssigneeName($assigneeName)
    {
        return $this->setData(self::ASSIGNEE_NAME, $assigneeName);
    }

    /**
     * Get assignee email
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getAssigneeEmail()
    {
        return $this->_get(self::ASSIGNEE_EMAIL);
    }

    /**
     * Set assignee email
     *
     * Notice:
     * This data is loaded from the admin_user table and cannot be saved via the message.
     *
     * @param string $assigneeEmail
     * @api
     *
     * @return $this
     */
    public function setAssigneeEmail($assigneeEmail)
    {
        return $this->setData(self::ASSIGNEE_EMAIL, $assigneeEmail);
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @api
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        return $this->setData(self::DELETED, $deleted);
    }

    /**
     * Get deleted
     *
     * @api
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->_get(self::DELETED);
    }

    /**
     * Set quote_id
     *
     * @param boolean $quoteId
     * @api
     *
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * Get quote_id
     *
     * @api
     * @return boolean
     */
    public function getQuoteId()
    {
        return $this->_get(self::QUOTE_ID);
    }

    /**
     * Set customer viewed at time
     *
     * @param string $customerViewedAt
     * @api
     *
     * @return $this
     */
    public function setCustomerViewedAt($customerViewedAt)
    {
        return $this->setData(self::CUSTOMER_VIEWED_AT, $customerViewedAt);
    }

    /**
     * Get customer viewed at time
     *
     * @api
     * @return string|null
     */
    public function getCustomerViewedAt()
    {
        return $this->_get(self::CUSTOMER_VIEWED_AT);
    }
}
