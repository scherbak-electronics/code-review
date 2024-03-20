<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Ticket;

use Exception;
use \Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Review\Model\Review;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Create
 * @package Cart2Quote\Desk\Controller\Ticket
 */
class Create extends \Magento\Framework\App\Action\Action
{
    const LIST_MESSAGE_BLOCK = 'ticket.message';

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $ticketSession;

    /**
     * Catalog catgory model
     *
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Desk model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $ticketRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $ticketFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $messageRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $messageFactory;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * Create constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Session\Generic $ticketSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \Magento\Customer\Model\CustomerFactory $customerModelFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Session\Generic $ticketSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->ticketSession = $ticketSession;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->ticketFactory = $ticketFactory;
        $this->messageRepositoryInterface = $messageRepositoryInterface;
        $this->messageFactory = $messageFactory;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->redirect = $context->getRedirect();
        $this->customerModelFactory = $customerModelFactory;
    }

    /**
     * Submit new ticket action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $layout = $this->resultFactory->create(ResultFactory::TYPE_PAGE)->getLayout();
        $postData = $this->getRequest()->getParams();
        $response = [
            'errors' => true,
            'ticket_id' => 0,
            'message_id' => 0
        ];

        try {
            $customer = $this->getCustomer();
            $ticket = $this->getTicket($customer->getId());
            $message = $this->saveMessage($ticket);

            $this->setSuccessMessage($layout, $ticket);

            if ($message && $ticket) {
                $this->dispatchNewTicketEvent($message, $ticket);
            }

            if (isset($postData['redirect_to_ticket']) && $postData['redirect_to_ticket']) {
                $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('desk/customer/view/', ['id' => $ticket->getId()]);
                return $resultRedirect;
            }

            if (isset($postData['refresh_page']) && $postData['refresh_page']) {
                $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->redirect->getRefererUrl());
                $this->messageManager->addSuccessMessage(__("Your message has been successfully sent."));
                return $resultRedirect;
            }

            $response['ticket_id'] = $ticket->getId();
            $response['message_id'] = $message->getId();
            $response['errors'] = false;
            $httpCode = 200;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $httpCode = 400;
        }

        $layout->initMessages();
        $response['messages'] = $layout->getMessagesBlock()->getGroupedHtml();

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setHttpResponseCode($httpCode)->setData($response);
    }

    /**
     * Initialize and check product
     *
     * @return \Magento\Catalog\Model\Product|bool
     */
    protected function initProduct()
    {
        $this->_eventManager->dispatch('ticket_controller_product_init_before', ['controller_action' => $this]);
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId = (int)$this->getRequest()->getParam('id');

        $product = $this->loadProduct($productId);
        if (!$product) {
            return false;
        }

        if ($categoryId) {
            $category = $this->categoryRepository->get($categoryId);
            $this->coreRegistry->register('current_category', $category);
        }

        try {
            $this->_eventManager->dispatch('ticket_controller_product_init', ['product' => $product]);
            $this->_eventManager->dispatch(
                'ticket_controller_product_init_after',
                ['product' => $product, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
            return false;
        }

        return $product;
    }

    /**
     * Load product model with data by passed id.
     * Return false if product was not loaded or has incorrect status.
     *
     * @param int $productId
     * @return bool|CatalogProduct
     */
    protected function loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        if ($this->coreRegistry->registry('current_product')) {
            $product = $this->coreRegistry->registry('current_product');
        } elseif ($this->coreRegistry->registry('product')) {
            $product = $this->coreRegistry->registry('product');
        } else {
            try {
                $product = $this->productRepository->getById($productId);
                if (!$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
                    throw new NoSuchEntityException();
                }
            } catch (NoSuchEntityException $noEntityException) {
                return false;
            }

            $this->coreRegistry->register('current_product', $product);
            $this->coreRegistry->register('product', $product);
        }

        return $product;
    }

    /**
     * Try to save the customer, if the customer already exists throw error.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function saveCustomer()
    {
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($this->storeManager->getWebsite()->getId());

        if ($postData = $this->getRequest()->getParam('customer')) {
            if ($postData['firstname']) {
                $customer->setFirstname($postData['firstname']);
            }

            if ($postData['lastname']) {
                $customer->setLastname($postData['lastname']);
            }

            if ($postData['email']) {
                $customer->setEmail($postData['email']);
            }
        }

        try {
            $customer = $this->accountManagement->createAccount($customer);
        } catch (AlreadyExistsException $e) {
            throw new \Magento\Framework\Exception\InputException(__('This email already exists in this store.'));
        } catch (InputMismatchException $e) {
            throw new \Magento\Framework\Exception\InputException(__('This email already exists in this store.'));
        }

        return $customer;
    }

    /**
     * Save the ticket
     *
     * @param int $customerId
     * @return bool|\Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function saveTicket($customerId)
    {
        $ticket = $this->ticketFactory->create();
        $ticket->setStoreId($this->storeManager->getStore()->getId());
        $ticket->setCustomerId($customerId);
        $ticket->setStatusId(1);

        if ($postData = $this->getRequest()->getParam('ticket')) {
            if ($postData['subject']) {
                $ticket->setSubject($postData['subject']);
            }
        }

        if ($product = $this->initProduct()) {
            $ticket->setSubject(__(sprintf('A question about product: %s', $product->getName())));
        }

        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $ticket->setQuoteId($quoteId);
        }

        $this->dispatchSaveTicketEventBefore($ticket);
        $ticket = $this->ticketRepositoryInterface->save($ticket);
        $this->dispatchSaveTicketEventAfter($ticket);

        return $ticket;
    }

    /**
     * Save the message
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return bool|\Cart2Quote\Desk\Api\Data\MessageInterface
     */
    protected function saveMessage(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $message = $this->messageFactory->create();
        if ($postData = $this->getRequest()->getParam('message')) {
            if (isset($postData['message'])) {
                $message->setMessage($postData['message']);
            }
        }

        $message
            ->setTicketId($ticket->getId())
            ->setCustomerId($ticket->getCustomerId())
            ->setIsPrivate(false);

        $this->dispatchSaveMessageEventBefore($message, $ticket);
        $message = $this->messageRepositoryInterface->save($message);
        $this->dispatchSaveMessageEventAfter($message, $ticket);
        return $message;
    }

    /**
     * Dispatch new ticket event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchNewTicketEvent(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_frontend_new_ticket',
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
    private function dispatchSaveTicketEventAfter(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_frontend_save_ticket_after',
            ['ticket' => $ticket]
        );
    }

    /**
     * Dispatch save ticket before event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    private function dispatchSaveTicketEventBefore(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_frontend_save_ticket_before',
            ['ticket' => $ticket]
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
     * Set success message
     *
     * @param \Magento\Framework\View\Layout $layout
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return $this
     */
    protected function setSuccessMessage(
        $layout,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $state = 'created';
        $url = $this->_url->getUrl('desk/customer/view', ['id' => $ticket->getId()]);

        $layout->getMessagesBlock()->addSuccess(
            __('Thank you for reaching out to us.') .
            ' ' .
            __(
                "<a href=\"%1\">Your ticket</a> has been $state.",
                $url
            )
        );

        return $this;
    }

    /**
     * Get the customer by session or save the new customer
     *
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        if ($this->customerSession->getCustomerId() == null) {
            //check if this customer already exits
            if ($postData = $this->getRequest()->getParam('customer')) {
                if ($postData['email']) {
                    $customer = $this->customerModelFactory->create();
                    $customer->setWebsiteId($this->storeManager->getWebsite()->getId());
                    $customer->loadByEmail($postData['email']);
                    if ($customer->getId()) {
                        return $customer;
                    }
                }
            }

            //customer doesn't exit by email, so make a new one
            $customer = $this->saveCustomer();
        } else {
            $customer = $this->customerSession->getCustomer();
        }

        return $customer;
    }

    /**
     * Get a single message HTML
     * The key set to the message indicates it's the first message in the list
     *
     * @param \Magento\Framework\View\Layout $layout
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return string
     */
    protected function renderMessage(
        $layout,
        \Cart2Quote\Desk\Api\Data\MessageInterface $message
    ) {
        $html = '';

        if ($block = $layout->getBlock(self::LIST_MESSAGE_BLOCK)) {
            $html = $block->setKey(\Cart2Quote\Desk\Block\Customer\Ticket\View\Message::FIRST_MESSAGE_KEY)
                ->setMessage($message)
                ->toHtml();
        }

        return $html;
    }

    /**
     * Create the ticket or load by ID
     *
     * @param int $customerId
     * @return bool|\Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function getTicket($customerId)
    {
        return $this->saveTicket($customerId);
    }
}
