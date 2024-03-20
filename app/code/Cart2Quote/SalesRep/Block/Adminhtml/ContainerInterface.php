<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Block\Adminhtml;

/**
 * Interface ContainerInterface
 * @package Cart2Quote\SalesRep\Block\Adminhtml
 */
interface ContainerInterface
{

    /**
     * Get the ID of the associated object
     *
     * @return int
     */
    public function getId();

    /**
     * Get the type id
     *
     * @return string
     */
    public function getTypeId();

    /**
     * Get the customer id
     *
     * @return int
     */
    public function getCustomerId();
}
