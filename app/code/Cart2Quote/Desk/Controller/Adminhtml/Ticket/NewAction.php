<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Review\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class NewAction
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $helperData;

    /**
     * Class NewAction constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helperData,
        Action\Context $context
    ) {
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * Render new ticket form
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helperData->getDeskEnabled()) {
            $this->getMessageManager()->addErrorMessage(
                __("Customer Support Desk is currently disabled. " .
                    "Please contact your Magento administrator to enable Customer Support Desk again.")
            );
            return $this->_redirect('admin/dashboard');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Cart2Quote_Desk::desk_tickets');
        $editBlock = $resultPage->getLayout()->createBlock('Cart2Quote\Desk\Block\Adminhtml\Edit');
        $resultPage->addContent($editBlock);
        $resultPage->getConfig()->getTitle()->prepend($editBlock->getHeaderText());
        return $resultPage;
    }
}
