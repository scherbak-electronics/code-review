<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Magento\Contact;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Url;

/**
 * Class Success
 * @package Cart2Quote\Desk\Block\Magento\Contact
 */
class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Success Block constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }

    /**
     * Get ticket create action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl(
            'desk/ticket/create',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getProductId(),
            ]
        );
    }

    /**
     * Get the logged in customer
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->currentCustomer->getCustomerId();
    }

    /**
     * Force disable cache
     *
     * @return bool
     */
    protected function _loadCache()
    {
        return false;
    }
}
