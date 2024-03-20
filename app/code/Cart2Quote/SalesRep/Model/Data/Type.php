<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model\Data;

/**
 * Class Type
 * @package Cart2Quote\SalesRep\Model\Data
 */
class Type extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Cart2Quote\SalesRep\Api\Data\TypeInterface
{

    /**
     * Get type id
     *
     * @api
     * @return int
     */
    public function getTypeId()
    {
        return $this->_get(self::TYPE_ID);
    }

    /**
     * Set type id
     *
     * @param int $typeId
     * @api
     *
     * @return $this
     */
    public function setTypeId($typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    /**
     * Get deleted
     *
     * @api
     * @return bool
     */
    public function getDeleted()
    {
        return $this->_get(self::DELETED);
    }

    /**
     * Set deleted
     *
     * @param bool $deleted
     * @api
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        return $this->setData(self::DELETED, $deleted);
    }

}
