<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Customer\Message;

use Magento\Framework\App\Action\Context;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Create
 * @package Cart2Quote\Desk\Controller\Customer\Message
 */
class Create extends \Magento\Framework\App\Action\Action
{
    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageInterfaceFactory
     */
    protected $messageInterfaceFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Class Create constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        Context $context
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messageInterfaceFactory = $messageInterfaceFactory;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context);
    }

    /**
     * Render my tickets
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $ticketId = $this->getRequest()->getParam('id');
        $message = $this->getRequest()->getParam('message');
        if (is_array($message) && isset($message['message'])) {
            $message = $message['message'];
        }
        $customerId = $this->currentCustomer->getCustomerId();

        $messageData = $this->messageInterfaceFactory->create();
        $messageData->setTicketId($ticketId);
        $messageData->setCustomerId($customerId);
        $messageData->setMessage($message);

        $resultJson = $this->resultJsonFactory->create();

        try{
            $httpCode = 200;
            $response = [
                'errors' => false,
                'message' => __("Message send.")
            ];
            $this->messageRepositoryInterface->save($messageData);
        } catch(Exception $e) {
            $httpCode = 400;
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }

        return $resultJson->setHttpResponseCode($httpCode)->setData($response);
    }
}
