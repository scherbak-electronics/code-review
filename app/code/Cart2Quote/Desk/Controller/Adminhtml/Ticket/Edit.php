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
 * Class Edit
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $ticketFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $messageFactory;

    /**
     * Cart2Quote data helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $helperData;

    /**
     * CLass Edit constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helperData,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        Action\Context $context
    ) {
        $this->helperData = $helperData;
        $this->ticketFactory = $ticketFactory;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->messageFactory = $messageFactory;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Render the edit page
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
