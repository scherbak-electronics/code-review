<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Class Container
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
abstract class Container extends \Magento\Sales\Model\Order\Email\Container\Container implements IdentityInterface
{
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $scopeConfig,
            $storeManager
        );
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
}
