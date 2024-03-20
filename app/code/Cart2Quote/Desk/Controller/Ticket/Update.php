<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Cart2Quote\Desk\Controller\Ticket;

use Exception;
use Cart2Quote\Desk\Api\Data\MessageInterfaceFactory;
use Cart2Quote\Desk\Api\MessageRepositoryInterface;
use Cart2Quote\Desk\Api\TicketRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Create
 * @package Cart2Quote\Desk\Controller\Ticket
 */
class Update extends \Magento\Framework\App\Action\Action
{
    const LIST_MESSAGE_BLOCK = 'ticket.message';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * @var MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * @var MessageInterfaceFactory
     */
    protected $messageFactory;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Update constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param TicketRepositoryInterface $ticketRepositoryInterface
     * @param MessageRepositoryInterface $messageRepositoryInterface
     * @param MessageInterfaceFactory $messageFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        TicketRepositoryInterface $ticketRepositoryInterface,
        MessageRepositoryInterface $messageRepositoryInterface,
        MessageInterfaceFactory $messageFactory
    )
    {
        parent::__construct($context);
        $this->context = $context;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @return Update|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        return $this->messageIsSetOnPost() ? $this->processRequest() : $this->processBadRequest();
    }

    /**
     * @param $postData
     * @param $ticket
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface
     */
    private function saveMessage($postData, $ticket)
    {
        $message = $this->messageFactory->create();
        if (isset($postData['message'])) {
            $message->setMessage($postData['message']);
        }

        $message->setTicketId($ticket->getId())
            ->setCustomerId($ticket->getCustomerId())
            ->setIsPrivate(false);
        $this->dispatchSaveMessageEventBefore($message, $ticket);
        $message = $this->messageRepositoryInterface->save($message);

        return $message;
    }

    /**
     * @param $messagePostData
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function processRequest()
    {
        $response = [
            'errors' => true,
            'ticket_id' => 0,
            'message_id' => 0
        ];

        try {
            $this->layout = $this->resultFactory->create(ResultFactory::TYPE_PAGE)->getLayout();
            $postData = $this->getRequest()->getParams();
            if (isset($postData['message']) && $postData['message']) {
                $messagePostData = $postData['message'];
            } else {
                throw new Exception("Message data not set on postData");
            }

            $ticket = $this->getTicket();
            $message = $this->saveMessage($messagePostData, $ticket);

            if (isset($postData['render_message']) && $postData['render_message']) {
                $response['message_html'] = $this->renderMessage($message);
            }

            $response['ticket_id'] = $ticket->getId();
            $response['message_id'] = $message->getId();
            $response['errors'] = false;

            $this->setSuccessMessage($ticket);

            $response['messages'] = $this->layout->getMessagesBlock()->getGroupedHtml();
            $httpCode = \Zend\Http\Response::STATUS_CODE_200;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $httpCode = \Zend\Http\Response::STATUS_CODE_400;
        }
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setHttpResponseCode($httpCode)->setData($response);
    }

    /**
     * @return $this
     */
    private function processBadRequest()
    {
        $httpCode = \Zend\Http\Response::STATUS_CODE_400;

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setHttpResponseCode($httpCode);
    }

    /**
     * @return bool
     */
    private function messageIsSetOnPost()
    {
        $messagePostData  = $this->getRequest()->getParam('message');
        return isset($messagePostData['message']);
    }

    /**
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return string
     */
    private function renderMessage(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message
    ) {
        $html = '';

        if ($block = $this->layout->getBlock(self::LIST_MESSAGE_BLOCK)) {
            $html = $block->setKey(\Cart2Quote\Desk\Block\Customer\Ticket\View\Message::FIRST_MESSAGE_KEY)
                ->setMessage($message)
                ->toHtml();
        }

        return $html;
    }

    /**
     * @param $ticket
     * @return $this
     */
    private function setSuccessMessage($ticket)
    {
        $state = 'updated';
        $url = $this->_url->getUrl('desk/customer/view', ['id' => $ticket->getId()]);

        $this->layout->getMessagesBlock()->addSuccess(
            __('Thank you for reaching out to us.') .
            ' ' .
            __(
                "<a href=\"%1\">Ticket #%2 (%3) has been ".$state.'.</a>',
                $url,
                $ticket->getId(),
                $ticket->getSubject()
            )
        );

        return $this;
    }

    /**
     * Dispatch save message after event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchSaveMessageEventAfter(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_frontend_save_message_after',
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
    private function dispatchSaveMessageEventBefore(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_frontend_save_message_before',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTicket()
    {
        return $this->ticketRepositoryInterface->getById($this->getRequest()->getParam('ticket_id'));
    }

}