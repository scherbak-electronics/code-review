<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Model;

/**
 * Class User
 * @package Cart2Quote\SalesRep\Model
 */
class User extends \Magento\Framework\Model\AbstractModel
{
    /**
     * User factory
     *
     * @var \Cart2Quote\SalesRep\Model\TypeFactory
     */
    protected $userFactory;

    /**
     * Data Object Helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Data Object Processor
     *
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * User constructor.
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\User $resource
     * @param \Cart2Quote\SalesRep\Model\ResourceModel\User\Collection $resourceCollection
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\SalesRep\Api\Data\UserInterfaceFactory $userFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\SalesRep\Model\ResourceModel\User $resource,
        \Cart2Quote\SalesRep\Model\ResourceModel\User\Collection $resourceCollection,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->userFactory = $userFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\SalesRep\Model\ResourceModel\User');
    }

    /**
     * Retrieve User model with User data
     *
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    public function getDataModel()
    {
        $data = $this->getData();
        $userDataObject = $this->userFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $userDataObject,
            $data,
            '\Cart2Quote\SalesRep\Api\Data\UserInterface'
        );
        $userDataObject->setId($this->getId());
        return $userDataObject;
    }

    /**
     * Update User data
     *
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterface $user
     * @return $this
     */
    public function updateData(\Cart2Quote\SalesRep\Api\Data\UserInterface $user)
    {
        $userDataAttributes = $this->dataObjectProcessor->buildOutputDataArray(
            $user,
            '\Cart2Quote\SalesRep\Api\Data\UserInterface'
        );

        foreach ($userDataAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $customAttributes = $user->getCustomAttributes();
        if ($customAttributes !== null) {
            foreach ($customAttributes as $attribute) {
                $this->setDataUsingMethod($attribute->getAttributeCode(), $attribute->getValue());
            }
        }

        return $this;
    }
}
