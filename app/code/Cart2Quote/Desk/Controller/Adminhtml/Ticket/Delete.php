<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

/**
 * Class Delete
 * @package Cart2Quote\Desk\Controller\Adminhtml\Ticket
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Class delete constructor
     *
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Deletes a ticket by ID
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $ticketId = $this->getRequest()->getParam('id', false);
        try {
            $this->dispatchDeleteTicketEventBefore($ticketId);
            $this->ticketRepositoryInterface->deleteById($ticketId);
            $this->dispatchDeleteTicketEventAfter($ticketId);
            $resultRedirect->setPath('desk/ticket/index/');
            $this->messageManager->addSuccessMessage(__("The ticket has been deleted."));
            return $resultRedirect;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __("Something went wrong deleting this ticket."));
        }

        return $resultRedirect->setPath('desk/ticket/edit/', ['id' => $ticketId]);
    }

    /**
     * Dispatch delete ticket after event
     *
     * @param int $ticketId
     * @return void
     */
    private function dispatchDeleteTicketEventAfter($ticketId)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_delete_ticket_after',
            ['ticket_id' => $ticketId]
        );
    }

    /**
     * Dispatch delete ticket before event
     *
     * @param int $ticketId
     * @return void
     */
    private function dispatchDeleteTicketEventBefore($ticketId)
    {
        $this->_eventManager->dispatch(
            'desk_adminhtml_delete_ticket_before',
            ['ticket_id' => $ticketId]
        );
    }
}
