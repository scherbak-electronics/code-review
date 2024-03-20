<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Interface IdentityInterface
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
interface IdentityInterface extends \Magento\Sales\Model\Order\Email\Container\IdentityInterface
{
    /**
     * Send copy to sales rep setting
     *
     * @return bool
     */
    public function isSendCopyToSalesRep();

    /**
     * Getter for the reciever email
     *
     * @return string
     */
    public function getRecieverEmail();

    /**
     * Getter for the reciever name
     *
     * @return string
     */
    public function getRecieverName();
}
