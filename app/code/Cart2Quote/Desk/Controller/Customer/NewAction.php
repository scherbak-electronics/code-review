<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Customer;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class NewAction
 * @package Cart2Quote\Desk\Controller\Customer
 */
class NewAction extends \Cart2Quote\Desk\Controller\Customer\Customer
{
    /**
     * Render new ticket page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('desk/customer');
        }

        $this->setTitle($resultPage);

        return $resultPage;
    }

    /**
     * Sets the title on the new ticket page.
     *
     * @param \Magento\Framework\View\Result\Page $resultPage
     *
     * @return void
     */
    protected function setTitle(\Magento\Framework\View\Result\Page $resultPage)
    {
        $title = __("Create New Ticket");
        $resultPage->getConfig()->getTitle()->set($title);
    }
}
