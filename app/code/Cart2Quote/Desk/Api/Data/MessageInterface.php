<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Desk\Api\Data;

/**
 * Interface MessageInterface
 * @package Cart2Quote\Desk\Api\Data
 */
interface MessageInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TICKET_ID = 'ticket_id';
    const CUSTOMER_ID = 'customer_id';
    const USER_ID = 'user_id';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const IS_PRIVATE = 'is_private';
    const STORE_ID = 'store_id';
    const MESSAGE = 'message';
    const EMAIL = 'email';
    const NAME = 'name';

    /**
     * Get message id
     *
     * @api
     * @return int
     */
    public function getId();

    /**
     * Set message id
     *
     * @param int $id
     * @api
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get ticket id
     *
     * @api
     * @return int|null
     */
    public function getTicketId();

    /**
     * Set ticket id
     *
     * @param int $ticketId
     * @api
     *
     * @return $this
     */
    public function setTicketId($ticketId);

    /**
     * Get customer id
     *
     * @api
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @api
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get priority id
     *
     * @api
     * @return bool
     */
    public function getIsPrivate();

    /**
     * Set priority id
     *
     * @param bool $isPrivate
     * @api
     *
     * @return $this
     */
    public function setIsPrivate($isPrivate);

    /**
     * Get message
     *
     * @api
     * @return string
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $message
     * @api
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get created at time
     *
     * @api
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @api
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at time
     *
     * @api
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated at time
     *
     * @param string $updatedAt
     * @api
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get user id
     *
     * @api
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user id
     *
     * @param string $userId
     * @api
     *
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get name
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @param string $name
     * @api
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get email
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @api
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * Notice:
     * This data is loaded from the admin_user or customer_entity table and cannot be saved via the message.
     *
     * @param string $email
     * @api
     *
     * @return $this
     */
    public function setEmail($email);
}
