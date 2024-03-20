<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Cart2Quote\DeskMessageTemplate\Api\Data\MessageInterface;
use Cart2Quote\DeskMessageTemplate\Model\ResourceModel\Message as ResourceMessage;

/**
 * Class Message
 * @package Cart2Quote\DeskMessageTemplate\Model
 */
class Message extends AbstractModel implements MessageInterface
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 0;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ResourceMessage|null $resource
     * @param ResourceMessage\Collection|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ResourceMessage $resource = null,
        ResourceMessage\Collection $resourceCollection = null
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(ResourceMessage::class);
    }

    /**
     * Retrieve message id
     *
     * @return int
     */
    public function getMessageId()
    {
        return $this->getData(self::MESSAGE_ID);
    }

    /**
     * Set Message ID
     *
     * @param int $id
     * @return MessageInterface
     */
    public function setMessageId($id)
    {
        return $this->setData(self::MESSAGE_ID, $id);
    }

    /**
     * Retrieve title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return MessageInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Retrieve content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return MessageInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Retrieve message creation time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return MessageInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Retrieve message update time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return MessageInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get Is active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return MessageInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, (string) $isActive);
    }

    /**
     * Prepare message's statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
