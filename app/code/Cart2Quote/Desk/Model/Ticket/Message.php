<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Model\Ticket;

use Magento\Catalog\Model\Product;

/**
 * Class Message
 * @package Cart2Quote\Desk\Model\Ticket
 */
class Message extends \Magento\Framework\Model\AbstractModel
{
    const OWNER_TYPE_USER = 'user';
    const OWNER_TYPE_CUSTOMER = 'customer';

    /**
     * Message Interface Factory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $messageInterfaceFactory;

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

    /**
     * Customer Repository Interface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * Customer Name API Interface
     *
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    protected $customerName;

    /**
     * User Model
     *
     * @var \Magento\User\Model\User
     */
    protected $user;

    /**
     * Customer API Interface
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customer;

    /** @var \Magento\User\Model\ResourceModel\User  */
    protected $userResource;

    /** @var \Magento\User\Model\UserFactory  */
    protected $userFactory;

    /**
     * Class message constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message $resource
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection $resourceCollection
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Api\CustomerNameGenerationInterface $customerName
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\User\Model\User $user
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message $resource,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection $resourceCollection,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\CustomerNameGenerationInterface $customerName,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        \Magento\User\Model\ResourceModel\User $userResourceModel,
        \Magento\User\Model\UserFactory $userFactory,
        array $data = []
    ) {
        $this->messageInterfaceFactory = $messageInterfaceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerName = $customerName;
        $this->userResource = $userResourceModel;
        $this->userFactory = $userFactory;
        $this->customer = $customer;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\ResourceModel\Ticket\Message');
    }

    /**
     * Retrieve Message model with message data
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface
     */
    public function getDataModel()
    {
        $messageData = $this->getData();
        $messageDataObject = $this->messageInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $messageDataObject,
            $messageData,
            '\Cart2Quote\Desk\Api\Data\MessageInterface'
        );
        $messageDataObject->setId($this->getId());
        return $messageDataObject;
    }

    /**
     * Update message data
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return $this
     */
    public function updateData(\Cart2Quote\Desk\Api\Data\MessageInterface $message)
    {
        $messageDataAttributes = $this->dataObjectProcessor->buildOutputDataArray(
            $message,
            '\Cart2Quote\Desk\Api\Data\MessageInterface'
        );

        foreach ($messageDataAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $customAttributes = $message->getCustomAttributes();
        if ($customAttributes !== null) {
            foreach ($customAttributes as $attribute) {
                $this->setDataUsingMethod($attribute->getAttributeCode(), $attribute->getValue());
            }
        }

        $messageId = $message->getId();
        if ($messageId) {
            $this->setId($messageId);
        }

        return $this;
    }

    /**
     * Get the type message owner depending on the customer_id or user_id
     *
     * @return string
     */
    public function getOwnerType()
    {
        if ($this->getUserId()) {
            return self::OWNER_TYPE_USER;
        } else {
            return self::OWNER_TYPE_CUSTOMER;
        }
    }

    /**
     * Loads the email depending on the user_id or the customer_id
     *
     * @return $this
     */
    public function loadEmail()
    {
        if ($this->getEmail() == null) {
            if ($this->getOwnerType() == self::OWNER_TYPE_USER) {
                $this->setEmail($this->getUserFromRepository($this->getUserId())->getEmail());
            } else {
                $this->setEmail($this->getCustomerFromRepository($this->getCustomerId())->getEmail());
            }
        }

        return $this;
    }

    /**
     * Loads the email depending on the user_id or the customer_id
     *
     * @return $this
     */
    private function loadCustomerEmail($customer)
    {
        if ($this->getEmail() == null) {
            $this->setEmail($customer->getEmail());
        }

        return $this;
    }

    /**
     * Loads the email depending on the user_id or the customer_id
     *
     * @return $this
     */
    private function loadUserEmail($user)
    {
        if ($this->getEmail() == null) {
            $this->setEmail($user->getEmail());
        }

        return $this;
    }

    /**
     * @param $customer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function loadCustomerName($customer)
    {
        if ($this->getName() == null) {
            $this->setName($this->customerName->getCustomerName($customer));
        }

        return $this;
    }

    /**
     * @param $user
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function loadUserName($user)
    {
        if ($this->getUserFirstname() == null && $this->getUserLastname() == null) {
            $this->setName($user->getName());
        } else {
            $this->setName($this->getUserFirstname() . ' ' . $this->getUserLastname());
        }

        return $this;
    }

    /**
     * Load name and email on the message
     *
     * @return $this
     */
    public function _afterLoad()
    {
        if ($this->getOwnerType() == self::OWNER_TYPE_USER) {
            $this->loadUserInformation();
        } else {
            $this->loadCustomerInformation();
        }

        return parent::_afterLoad();
    }

    /**
     * @param $assigneeId
     * @return \Magento\User\Model\User
     */
    private function getUserFromRepository($assigneeId)
    {
        $user = $this->userFactory->create();
        $this->userResource->load($user, $assigneeId);

        return $user;
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerFromRepository($customerId)
    {
        return $this->customerRepositoryInterface->getById($customerId);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function loadUserInformation()
    {
        if ($this->getUserId()) {
            $user = $this->getUserFromRepository($this->getUserId());
            $this->loadUserEmail($user);
            $this->loadUserName($user);
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function loadCustomerInformation()
    {
        if ($this->getCustomerId()) {
            $customer = $this->getCustomerFromRepository($this->getCustomerId());
            $this->loadCustomerEmail($customer);
            $this->loadCustomerName($customer);
        }
    }
}
