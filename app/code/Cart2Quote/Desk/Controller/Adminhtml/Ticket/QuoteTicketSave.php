<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QuoteTicketSave
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class QuoteTicketSave extends \Cart2Quote\Desk\Controller\Adminhtml\Ticket\Save
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $quoteSession;
    /**
     * QuoteTicketSave constructor.
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Cart2Quote\Desk\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        Action\Context $context
    ) {
        parent::__construct(
            $helperData,
            $adminSession,
            $ticketFactory,
            $ticketRepositoryInterface,
            $messageFactory,
            $messageRepositoryInterface,
            $context
        );
        $this->quoteSession = $quoteSession;
    }

    /**
     * Save/update the ticket
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helperData->getDeskEnabled()) {
            $this->getMessageManager()->addErrorMessage(
                __(
                    "Customer Support Desk is currently disabled. " .
                    "Please contact your Magento administrator to enable Customer Support Desk again."
                )
            );

            return $this->_redirect('admin/dashboard');
        }
        $status = $this->getRequest()->getParam('status_id');
        $resultRedirect =  $this->resultRedirectFactory->create();
        if (isset($status)) {
            try {
                $isNew = $this->getRequest()->getParam('id') == 0;
                $this->setIsNew($isNew);
                $ticket = $this->saveTicket();
                $message = $this->saveMessage($ticket);
                $this->getRequest()->setParams(['id' => $ticket->getId()]);
                $this->setSuccessMessage($ticket);

                if ($this->isNew && $message && $ticket) {
                    $this->dispatchNewTicketEvent($message, $ticket);
                }
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                $resultRedirect->setPath('quotation/quote/index');
                return $resultRedirect;
            }
        }

        $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getQuotationQuoteId()]);
        return $resultRedirect;
    }

    /**
     * Update or save the ticket
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function saveTicket()
    {
        $ticket = $this->ticketRepositoryInterface->getById($this->getRequest()->getParam('id'));
        $defaultValues = $this->fillDefaultValues($ticket);

        if ($this->getIsNew()) {
            $ticket = $defaultValues;
        }
        $ticket->setStatusId($this->getRequest()->getParam('status_id'));
        $ticket->setAssigneeId($this->getAssigneeId());
        $ticket->setSubject($this->getRequest()->getParam('subject'));
        $ticket->setCustomerId($this->getCustomerId());
        $ticket->setStoreId($this->getStoreId());
        $ticket->setQuoteId($this->getQuotationQuoteId());

        $this->dispatchSaveTicketEventBefore($ticket);
        $ticket = $this->ticketRepositoryInterface->save($ticket);
        $this->dispatchSaveTicketEventAfter($ticket);

        return $ticket;
    }

    /**
     * Save a new message.
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return bool|\Cart2Quote\Desk\Api\Data\MessageInterface
     */
    protected function saveMessage(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $message = false;
        if ($this->getRequest()->getParam('message')) {
            $message = $this->messageFactory->create();
            $message->setTicketId($ticket->getId());
            $message->setMessage($this->getRequest()->getParam('message'));
            $message->setUserId($ticket->getAssigneeId());
            $message->setIsPrivate($this->getRequest()->getParam('is_private'));

            $this->dispatchSaveMessageEventBefore($message, $ticket);
            $message = $this->messageRepositoryInterface->save($message);
            $this->dispatchSaveMessageEventAfter($message, $ticket);
        }

        return $message;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        if (!$this->quoteSession->hasData('customer_id')) {
            throw new LocalizedException(__("Can't save Quote Ticket without the Customer Id being set on the session."));
        }

        return $this->quoteSession->getCustomerId();

    }

    /**
     * @return int
     */
    public function getQuotationQuoteId()
    {
        if (!$this->quoteSession->hasData('quotation_quote_id')) {
            throw new LocalizedException(__("Can't save Quote Ticket without the Quotation Quote Id being set on the session."));
        }

        return $this->quoteSession->getQuotationQuoteId();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->quoteSession->hasData('store_id')) {
            throw new LocalizedException(__("Can't save Quote Ticket without the Store Id being set on the session."));
        }

        return $this->quoteSession->getStoreId();
    }

    /**
     * @param $ticket
     * @return \Cart2Quote\Desk\Model\Data\Ticket
     */
    public function fillDefaultValues($ticket)
    {
        $ticket->setPriorityId(2);
        $ticket->setPriority(\Cart2Quote\Desk\Model\Ticket\Priority::PRIORITY_NORMAL);
        $ticket->setStatusId(1);
        $ticket->setStatus(\Cart2Quote\Desk\Model\Ticket\Status::STATUS_OPEN);

        return $ticket;
    }
}
