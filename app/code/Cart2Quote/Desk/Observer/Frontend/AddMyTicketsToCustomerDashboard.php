<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddMyTicketsToCustomerDashboard
 * @package Cart2Quote\Desk\Observer\Frontend
 */
class AddMyTicketsToCustomerDashboard implements ObserverInterface
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $helper;

    /**
     * Class AddMyTicketsToCustomerDashboard constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helper
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Adds the My Tickets to the customer Dashboard
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->isCustomerAccountNavigation($observer) && $this->helper->getDeskEnabled()) {
            $current = false;
            $moduleName = $observer->getBlock()->getRequest()->getModuleName();
            if ($moduleName == 'desk') {
                $current = true;
            }

            $observer->getBlock()->addChild(
                'customer-account-navigation-desk-tickets-link',
                'Magento\Framework\View\Element\Html\Link\Current',
                [
                    'path' => 'desk/customer',
                    'label' => 'My Tickets',
                    'current' => $current
                ]
            );
        }

        return $this;
    }

    /**
     * Check if this is the customer account navigation block
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     */
    protected function isCustomerAccountNavigation(\Magento\Framework\Event\Observer $observer)
    {
        return $observer->getBlock()->getNameInLayout() == 'customer_account_navigation';
    }
}
