<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Observer;

/**
 * Interface CollectionAbstractInterface
 * @package Cart2Quote\SalesRep\Observer
 */
interface CollectionAbstractInterface
{
    /**
     * Get the object type
     *
     * @return string
     */
    public function getTypeId();
}
