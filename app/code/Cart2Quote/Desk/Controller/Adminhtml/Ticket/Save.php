<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\InputException;
use Magento\Review\Controller\Adminhtml\Product as ProductController;

/**
 * Class Save
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class Save extends \Magento\Backend\App\Action
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
     * Flag to keep track if this is a new ticket
     *
     * @var boolean
     */
    protected $isNew;

    /**
     * Flag to keep track if this is a new ticket
     *
     * @var boolean
     */
    protected $adminSession;

    /**
     * Cart2Quote data helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $helperData;

    /**
     * Class Save constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        Action\Context $context
    ) {
        $this->helperData = $helperData;
        $this->adminSession = $adminSession;
        $this->ticketFactory = $ticketFactory;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->messageFactory = $messageFactory;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        parent::__construct($context);
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
        if (isset($status)) {
            try {
                $this->setIsNew($this->getRequest()->getParam('id') == 0);
                $ticket = $this->saveTicket();
                $message = $this->saveMessage($ticket);
                $this->getRequest()->setParams(['id' => $ticket->getId()]);
                $this->setSuccessMessage($ticket);

                if ($this->isNew && $message && $ticket) {
                    $this->dispatchNewTicketEvent($message, $ticket);
                }
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Update or save the ticket
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function saveTicket()
    {
        $ticket = $this->ticketRepositoryInterface->getById($this->getRequest()->getParam('id'));
        $ticket->setStatusId($this->getRequest()->getParam('status_id'));
        $ticket->setAssigneeId($this->getAssigneeId());
        $ticket->setPriorityId($this->getRequest()->getParam('priority_id'));
        $ticket->setCustomerId($this->getRequest()->getParam('customer_id'));
        $ticket->setStoreId($this->getRequest()->getParam('store_id'));
        $ticket->setSubject($this->getRequest()->getParam('subject'));

        $this->dispatchSaveTicketEventBefore($ticket);
        $ticket = $this->ticketRepositoryInterface->save($ticket);
        $this->dispatchSaveTicketEventAfter($ticket);

        return $ticket;
    }

    /**
     * Save a new message.
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
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
     * Set success message
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return $this
     */
    protected function setSuccessMessage(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $url = $this->_helper->getUrl('desk/ticket/edit', ['id' => $ticket->getId()]);
        if ($this->getIsNew()) {
            $successMessage = __("<a href=\"%1\">Ticket #%2 has been created.</a>", $url, $ticket->getId());
        } else {
            $successMessage = __("<a href=\"%1\">Ticket #%2 has been updated.</a>", $url, $ticket->getId());
        }

        $this->getMessageManager()->addSuccess($successMessage);
        return $this;
    }

    /**
     * Dispatch new ticket event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    protected function dispatchNewTicketEvent(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_new_ticket',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save message after event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    protected function dispatchSaveMessageEventAfter(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_message_after',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save message before event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    protected function dispatchSaveMessageEventBefore(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_message_before',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save ticket after event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    protected function dispatchSaveTicketEventAfter(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_ticket_after',
            ['ticket' => $ticket]
        );
    }

    /**
     * Dispatch save ticket before event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    protected function dispatchSaveTicketEventBefore(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_save_ticket_before',
            ['ticket' => $ticket]
        );
    }

    /**
     * Flag for a new ticket
     *
     * @param bool|false $isNew
     * @return $this
     */
    public function setIsNew($isNew = false)
    {
        $this->isNew = $isNew;
        return $this;
    }

    /**
     * Flag for a new ticket
     *
     * @return bool
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * Get assignee id
     *
     * @return int
     */
    public function getAssigneeId()
    {
        $assigneeId = $this->getRequest()->getParam('assignee_id');
        if ($assigneeId == 0) {
            $assigneeId = $this->adminSession->getUser()->getId();
        }

        return $assigneeId;
    }
}
