<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api\Data;

/**
 * Interface OrderInterface
 * @package Cart2Quote\SalesRep\Api\Data
 */
interface OrderInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Id
     */
    const ID = 'id';

    /**
     * User id
     */
    const USER_ID = 'user_id';

    /**
     * Order
     */
    const ORDER = 'order';

    /**
     * Store ID
     */
    const STORE_ID = 'store_id';

    /**
     * Get id
     *
     * @api
     * @return int
     */
    public function getId();

    /**
     * Set id
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
     * Get order
     *
     * @api
     * @return int
     */
    public function getOrder();

    /**
     * Set order
     *
     * @param int $order
     * @api
     *
     * @return $this
     */
    public function setOrder($order);

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);
}
