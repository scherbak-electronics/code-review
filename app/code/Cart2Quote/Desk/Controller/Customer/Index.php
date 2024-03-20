<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Customer;

use Magento\Review\Controller\Customer as CustomerController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Cart2Quote\Desk\Controller\Customer
 */
class Index extends \Cart2Quote\Desk\Controller\Customer\Customer
{
    /**
     * Render my tickets
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('desk/customer');
        }

        $resultPage->getConfig()->getTitle()->set(__("My Tickets"));
        return $resultPage;
    }
}
