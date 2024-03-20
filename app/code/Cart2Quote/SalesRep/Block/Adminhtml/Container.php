<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 */
namespace Cart2Quote\SalesRep\Block\Adminhtml;

/**
 * Class Container
 * @package Cart2Quote\SalesRep\Block\Adminhtml
 */
abstract class Container extends \Magento\Backend\Block\Template implements ContainerInterface
{
    /**
     * User Collection
     *
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    private $userCollection;

    /**
     *  User Repository
     *
     * @var \Cart2Quote\SalesRep\Api\UserRepositoryInterface
     */
    private $userRepository;

    /**
     * Container constructor.
     * @param \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository
     * @param \Magento\User\Model\ResourceModel\User\Collection $userCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\SalesRep\Api\UserRepositoryInterface $userRepository,
        \Magento\User\Model\ResourceModel\User\Collection $userCollection,
        \Magento\Backend\Block\Template\Context $context,
        array $data
    ) {
        $this->userRepository = $userRepository;
        $this->userCollection = $userCollection;
        parent::__construct($context, $data);
    }

    /**
     * Get the SalesRep Name
     *
     * @return string
     */
    public function getName()
    {
        $name = __('Not Assigned');

        return $name;
    }

    /**
     * Get the assign user action
     *
     * @return string
     */
    public function getUsersUrl()
    {
        return $this->getUrl('salesrep/user/assign');
    }

    /**
     * Get the salesrep
     *
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    public function getSalesRep()
    {
        return $this->userRepository->getMainUserByAssociatedId($this->getId(), $this->getTypeId());
    }

    /**
     * Get the sales rep user block
     *
     * @return bool|\Cart2Quote\SalesRep\Block\Adminhtml\SalesRep\User
     */
    public function getSalesRepUserBlock()
    {
        return $this->getChildBlock('salesrep_user');
    }

    /**
     * Get the sales rep HTML
     *
     * @return string
     */
    public function getSalesRepHtml()
    {
        $child = $this->getSalesRepUserBlock();
        $html = $child->setSalesRep($this->getSalesRep())->toHtml();
        return $html;
    }
}
