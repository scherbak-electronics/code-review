<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api\Data;

/**
 * Interface UserInterface
 * @package Cart2Quote\SalesRep\Api\Data
 */
interface UserInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const USER_ID = 'user_id';
    const OBJECT_ID = 'object_id';
    const TYPE_ID = 'type_id';
    const IS_MAIN = 'is_main';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';
    const DELETED = 'is_deleted';

    /**
     * Get ticket id
     *
     * @api
     * @return int
     */
    public function getId();

    /**
     * Set ticket id
     *
     * @param int $id
     * @api
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get user id
     *
     * @api
     * @return int
     */
    public function getUserId();

    /**
     * Set user id
     *
     * @param int $userId
     * @api
     *
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Get object id
     *
     * @api
     * @return int
     */
    public function getObjectId();

    /**
     * Set object id
     *
     * @param int $objectId
     * @api
     *
     * @return $this
     */
    public function setObjectId($objectId);

    /**
     * Get type id
     *
     * @api
     * @return string
     */
    public function getTypeId();

    /**
     * Set type id
     *
     * @param string $typeId
     * @api
     *
     * @return $this
     */
    public function setTypeId($typeId);

    /**
     * Get is main
     *
     * @api
     * @return bool
     */
    public function getIsMain();

    /**
     * Set is main
     *
     * @param bool $isMain
     * @api
     *
     * @return $this
     */
    public function setIsMain($isMain);

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
}
