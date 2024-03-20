<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Interface SalesRepIdentityInterface
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
interface SalesRepIdentityInterface
{
    /**
     * @return bool
     */
    public function isSendCopyToAssignedSalesRep();

    /**
     * @return string
     */
    public function getSender();
}
