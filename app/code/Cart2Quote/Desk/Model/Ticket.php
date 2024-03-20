<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model;

use Cart2Quote\Desk\Model\ResourceModel\Ticket\PriorityRepository;
use Cart2Quote\Desk\Model\ResourceModel\Ticket\StatusRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Ticket
 * @package Cart2Quote\Desk\Model
 *
 * Magic setters
 * @method setStatusId(int $id)
 * @method setCustomerId(int $id)
 * @method setAssigneeId(int $id)
 * @method setPriorityId(int $id)
 * @method setSubject(string $subject)
 *
 * Magic getters
 * @method int getStatusId()
 * @method int getCustomerId()
 * @method int getAssigneeId()
 * @method int getPriorityId()
 * @method String getSubject()
 * @method String getPriorityCode()
 * @method String getStatus()
 *
 */
class Ticket extends \Magento\Framework\Model\AbstractModel
{
    const TICKET_GRID_INDEXER_ID = 'ticket_grid';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'ticket_block';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'desk_ticket';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Cart2Quote helper
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $helper;

    /**
     * Customer Repository Interface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * Customer Name Api
     *
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    protected $customerName;

    /**
     * Ticket Interface Factory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $ticketInterfaceFactory;

    /**
     * Data Object Processor
     *
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Data Object Helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /** @var StatusRepository $statusRepository */
    protected $statusRepository;

    /** @var \Magento\User\Model\ResourceModel\User  */
    protected $userResource;

    /** @var \Magento\User\Model\UserFactory  */
    protected $userFactory;

    /**
     * Class Ticket constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Ticket|\Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param ResourceModel\Ticket\Collection|\Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Cart2Quote\Desk\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Api\CustomerNameGenerationInterface $customerName
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketInterfaceFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param StatusRepository $statusRepository
     * @param PriorityRepository $priorityRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket $resource,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Collection $resourceCollection,
        \Cart2Quote\Desk\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\CustomerNameGenerationInterface $customerName,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketInterfaceFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\User\Model\ResourceModel\User $userResourceModel,
        \Magento\User\Model\UserFactory $userFactory,
        StatusRepository $statusRepository,
        PriorityRepository $priorityRepository,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->userResource = $userResourceModel;
        $this->userFactory = $userFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerName = $customerName;
        $this->ticketInterfaceFactory = $ticketInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->statusRepository = $statusRepository;
        $this->priorityRepository = $priorityRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\ResourceModel\Ticket');
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->getData('store_id') === null) {
            $this->setStoreId($this->storeManager->getStore()->getId());
        }

        return $this->getData('store_id');
    }

    /**
     * Load the status on the ticket
     *
     * @return $this
     */
    public function loadStatus()
    {
        try {
            $status = $this->statusRepository->getById($this->getStatusId());
            $this->setStatus($status->getCode());
        } catch (NoSuchEntityException $e) {
            $this->setStatus(null);
        }

        return $this;
    }

    /**
     * Load the priority on the ticket
     *
     * @return $this
     */
    public function loadPriority()
    {
        try {
            $priority = $this->priorityRepository->getById($this->getPriorityId());
            $this->setPriority($priority->getCode());
        } catch (NoSuchEntityException $e) {
            $this->setPriority(null);
        }

        return $this;
    }

    /**
     * Retrieve ticket model with ticket data
     *
     * @return \Cart2Quote\Desk\Api\Data\TicketInterface
     */
    public function getDataModel()
    {
        $ticketData = $this->getData();
        $ticketDataObject = $this->ticketInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ticketDataObject,
            $ticketData,
            \Cart2Quote\Desk\Api\Data\TicketInterface::class
        );
        $ticketDataObject->setId($this->getId());
        return $ticketDataObject;
    }

    /**
     * Update ticket data
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return $this
     */
    public function updateData(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $ticketDataAttributes = $this->dataObjectProcessor->buildOutputDataArray(
            $ticket,
            \Cart2Quote\Desk\Api\Data\TicketInterface::class
        );

        foreach ($ticketDataAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $customAttributes = $ticket->getCustomAttributes();
        if ($customAttributes !== null) {
            foreach ($customAttributes as $attribute) {
                $this->setDataUsingMethod($attribute->getAttributeCode(), $attribute->getValue());
            }
        }

        $ticketId = $ticket->getId();
        if ($ticketId) {
            $this->setId($ticketId);
        }

        return $this;
    }

    /**
     * Load Assignee, Customer Name, Priority and Status
     *
     * @return $this
     */
    public function _afterLoad()
    {
        if ($this->getCustomerId()) {
            $customer = $this->getCustomerFromRepository($this->getCustomerId());
            $this->loadCustomerName($customer);
            $this->loadCustomerEmail($customer);
        }

        if ($this->getAssigneeId()) {
            $assignee = $this->getAssigneeFromRepository($this->getAssigneeId());
            $this->loadAssigneeName($assignee);
            $this->loadAssigneeEmail($assignee);
        }

        $this->loadPriority();
        $this->loadStatus();
        return parent::_afterLoad();
    }

    /**
     * Get the customer
     *
     * @return CustomerInterface
     */
    public function getCustomerFromRepository($customerId)
    {
        return $this->customerRepositoryInterface->getById($customerId);
    }

    /**
     * @param $assigneeId
     * @return \Magento\User\Model\User
     */
    public function getAssigneeFromRepository($assigneeId)
    {
        $user = $this->userFactory->create();
        $this->userResource->load($user, $assigneeId);

        return $user;
    }

    /**
     * Loads the full customer name
     *
     * @return $this
     */
    public function loadCustomerName(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        if (!$this->getCustomerName()) {
            $name = $this->customerName->getCustomerName($customer);
            $this->setCustomerName($name);
        }
        return $this;
    }

    /**
     * Loads the customer email
     *
     * @return $this
     */
    public function loadCustomerEmail(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        if (!$this->getCustomerEmail()) {
            $this->setCustomerEmail($customer->getEmail());
        }

        return $this;
    }

    /**
     * @param $assignee
     * @return $this
     */
    public function loadAssigneeName(\Magento\User\Model\User $assignee)
    {
        if (!$this->getAssigneeName()) {
            $this->setAssigneeName($assignee->getName());
        }

        return $this;
    }

    /**
     * @param $assignee
     * @return $this
     */
    public function loadAssigneeEmail(\Magento\User\Model\User $assignee)
    {
        if (!$this->getAssigneeEmail()) {
            $this->setAssigneeEmail($assignee->getEmail());
        }

        return $this;
    }
}
