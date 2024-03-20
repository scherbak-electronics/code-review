<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Api\Data;

/**
 * Interface MessageInterface
 * @package Cart2Quote\DeskMessageTemplate\Api\Data
 */
interface MessageInterface
{
    const MESSAGE_ID     = 'message_id';
    const TITLE          = 'title';
    const CONTENT        = 'content';
    const CREATED_AT  = 'created_at';
    const UPDATED_AT    = 'updated_at';
    const IS_ACTIVE      = 'is_active';

    /**
     * Get Message ID
     *
     * @return int|null
     */
    public function getMessageId();

    /**
     * Set Message ID
     *
     * @param int $id
     * @return MessageInterface
     */
    public function setMessageId($id);

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     * @return MessageInterface
     */
    public function setTitle($title);

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return MessageInterface
     */
    public function setContent($content);

    /**
     * Get created at time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @return MessageInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set update at time
     *
     * @param string $updatedAt
     * @return MessageInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get Is active
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return MessageInterface
     */
    public function setIsActive($isActive);
}