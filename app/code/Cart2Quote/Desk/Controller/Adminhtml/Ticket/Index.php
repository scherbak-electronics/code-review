<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class Index extends Action
{
    /**
     * Result Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Result Forward Factory
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $helperData;

    /**
     * Class index constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helperData,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        Action\Context $context
    ) {
        $this->helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Render ticket list view
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->helperData->getDeskEnabled()) {
            $this->getMessageManager()->addError(
                __("Customer Support Desk is currently disabled. " .
                    "Please contact your Magento administrator to enable Customer Support Desk again.")
            );
            return $this->_redirect('admin/dashboard');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Cart2Quote_Desk::desk_tickets');
        $resultPage->getConfig()->getTitle()->prepend(__("Tickets"));
        $resultPage->addContent($resultPage->getLayout()->createBlock('Cart2Quote\Desk\Block\Adminhtml\GridContainer'));
        return $resultPage;
    }
}
