<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer;

/**
 * Interface ModelAbstractInterface
 * @package Cart2Quote\SalesRep\Observer
 */
interface ModelAbstractInterface
{
    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId();

    /**
     * Get the user id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getUserId(\Magento\Framework\Model\AbstractModel $object);

    /**
     * Get the object id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    public function getObjectId(\Magento\Framework\Model\AbstractModel $object);
}
