<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 */
namespace Cart2Quote\SalesRep\Block\Adminhtml\SalesRep;

/**
 * Class User
 * @package Cart2Quote\SalesRep\Block\Adminhtml\SalesRep
 */
class User extends \Magento\Backend\Block\Template
{
    /**
     * The sales rep
     *
     * @var \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    protected $salesRep;

    /**
     * Magento admin user
     *
     * @var \Magento\User\Model\User
     */
    protected $user;

    /**
     * User constructor.
     * @param \Magento\User\Model\User $user
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\User\Model\User $user,
        \Magento\Backend\Block\Template\Context $context,
        array $data
    ) {
        $this->user = $user;
        parent::__construct($context, $data);
    }

    /**
     * Set an user
     *
     * @param \Cart2Quote\SalesRep\Api\Data\UserInterface $salesRep
     * @return $this
     */
    public function setSalesRep(\Cart2Quote\SalesRep\Api\Data\UserInterface $salesRep)
    {
        $this->salesRep = $salesRep;
        $this->user->load($salesRep->getUserId());

        return $this;
    }

    /**
     * Get the user
     *
     * @return \Cart2Quote\SalesRep\Api\Data\UserInterface
     */
    public function getSalesRep()
    {
        echo "<pre>"; print_r($this->salesRep); die("test5656");
        return $this->salesRep;
    }

    /**
     * Get Magento Admin User
     *
     * @return \Magento\User\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Check if SalesRep is set.
     *
     * @return bool
     */
    public function hasSalesRep()
    {
        return $this->user->getId() > 0;
    }

    /**
     * Get the SalesRep name
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {
        if ($this->hasSalesRep()) {
            return $this->getUser()->getName();
        } else {
            return '';
        }
    }

    /**
     * Get user role.
     *
     * @return string
     */
    public function getRole()
    {
        if ($this->hasSalesRep()) {
            return $this->getUser()->getRole()->getRoleName();
        } else {
            return '';
        }
    }

    /**
     * Get action label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getActionLabel()
    {
        if ($this->hasSalesRep()) {
            return __('Change');
        } else {
            return __('Assign a Sales Representative');
        }
    }

    /**
     * Get Show Sales Rep
     *
     * @return string
     */
    public function getShowSalesRep()
    {
        if ($this->hasSalesRep()) {
            return '';
        } else {
            return 'display: none;';
        }
    }
}
