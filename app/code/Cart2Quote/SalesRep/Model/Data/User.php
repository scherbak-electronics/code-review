<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Data;

/**
 * Class User
 * @package Cart2Quote\SalesRep\Model\Data
 */
class User extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Cart2Quote\SalesRep\Api\Data\UserInterface
{
    /**
     * Get ticket id
     *
     * @api
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set ticket id
     *
     * @param int $id
     * @api
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get user id
     *
     * @api
     * @return int
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @api
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get quote id
     *
     * @api
     * @return int
     */
    public function getObjectId()
    {
        return $this->_get(self::OBJECT_ID);
    }

    /**
     * Set user id
     *
     * @param int $objectId
     * @api
     *
     * @return $this
     */
    public function setObjectId($objectId)
    {
        return $this->setData(self::OBJECT_ID, $objectId);
    }

    /**
     * Get type id
     *
     * @api
     * @return string
     */
    public function getTypeId()
    {
        return $this->_get(self::TYPE_ID);
    }

    /**
     * Set type id
     *
     * @param string $typeId
     * @api
     *
     * @return $this
     */
    public function setTypeId($typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * Get is main
     *
     * @api
     * @return bool
     */
    public function getIsMain()
    {
        return $this->_get(self::IS_MAIN);
    }

    /**
     * Set is main
     *
     * @param bool $isMain
     * @api
     *
     * @return $this
     */
    public function setIsMain($isMain)
    {
        return $this->setData(self::IS_MAIN, $isMain);
    }

    /**
     * Get created at time
     *
     * @api
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created at time
     *
     * @param string $createdAt
     * @api
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at time
     *
     * @api
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Set updated at time
     *
     * @param string $updatedAt
     * @api
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
