<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\Data;

use Cart2Quote\SalesRep\Api\Data\OrderInterface;

/**
 * Class Order
 * @package Cart2Quote\SalesRep\Model\Data
 */
class Order extends \Magento\Framework\Api\AbstractExtensibleObject implements \Cart2Quote\SalesRep\Api\Data\OrderInterface
{
    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @param int $id
     * @return Order
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }

    /**
     * @param int $userId
     * @return Order
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * @return int|null
     */
    public function getOrder()
    {
        return $this->_get(self::ORDER);
    }

    /**
     * @param int $order
     * @return Order
     */
    public function setOrder($order)
    {
        return $this->setData(self::ORDER, $order);
    }

    /**
     * @return mixed|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * @param $storeId
     * @return Order
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}
