<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;

/**
 * Class Customer
 * @package Cart2Quote\Desk\Controller\Customer
 */
abstract class Customer extends Action
{
    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Cart2Quote Data Helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $dataHelper;

    /**
     * Customer Ticket Controller constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param \Cart2Quote\Desk\Helper\Data $dataHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Cart2Quote\Desk\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        if (!$this->dataHelper->getDeskEnabled()) {
            $this->_redirect('customer/account');
        }

        return parent::dispatch($request);
    }
}
