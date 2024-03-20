<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Customer;

use Cart2Quote\Desk\Model\Data\Ticket;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class View
 * @package Cart2Quote\Desk\Controller\Customer
 */
class View extends \Cart2Quote\Desk\Controller\Customer\Customer
{
    /**
     * Ticket model factory
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * View constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Desk\Helper\Data $dataHelper,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
    ) {
        parent::__construct($context, $customerSession, $dataHelper);
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
    }

    /**
     * Render ticket details
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->isCustomerAllowedToViewTicket()) {
            return $this->_redirect('desk/customer');
        }
        $this->setViewedTime();
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('desk/customer');
        }
        $this->setTitle($resultPage);

        return $resultPage;
    }

    /**
     * Sets the title on the view page.
     *
     * @param \Magento\Framework\View\Result\Page $resultPage
     *
     * @return void
     */
    protected function setTitle(\Magento\Framework\View\Result\Page $resultPage)
    {
        $ticketId = $this->getRequest()->getParam('id');
        $title = $ticketId ? __("Ticket #%1", $ticketId) : __("Ticket Details");

        $resultPage->getConfig()->getTitle()->set($title);
    }

    /**
     * Check if allowed to view specific ticket
     *
     * @return bool
     */
    protected function isCustomerAllowedToViewTicket()
    {
        $ticketId = $this->getRequest()->getParam('id');
        if (isset($ticketId)) {
            /** @var Ticket $ticket */
            $ticket = $this->ticketRepositoryInterface->getById($ticketId);
            if ($ticket->getCustomerId() == $this->customerSession->getCustomerId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save current view time
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function setViewedTime()
    {
        $ticketId = $this->getRequest()->getParam('id');
        if (isset($ticketId)) {
            $ticket = $this->ticketRepositoryInterface->getById($ticketId);
            $ticket->setCustomerViewedAt(date('Y-m-d H:i:s'));
            $this->ticketRepositoryInterface->save($ticket);
        }
    }
}
