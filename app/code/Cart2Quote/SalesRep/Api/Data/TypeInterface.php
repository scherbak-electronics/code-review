<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Api\Data;

/**
 * Interface TypeInterface
 * @package Cart2Quote\SalesRep\Api\Data
 */
interface TypeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const TYPE_ID = 'type_id';

    /**
     * Deleted
     */
    const DELETED = 'deleted';

    /**
     * Get type id
     *
     * @api
     * @return int
     */
    public function getTypeId();

    /**
     * Set type id
     *
     * @param int $typeId
     * @api
     *
     * @return $this
     */
    public function setTypeId($typeId);

    /**
     * Get deleted
     *
     * @api
     * @return boolean
     */
    public function getDeleted();

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @api
     *
     * @return $this
     */
    public function setDeleted($deleted);
}
