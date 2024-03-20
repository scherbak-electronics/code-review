<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Cart2Quote
 */

namespace Cart2Quote\AutoProposal\Model\Quote\Email\Container;

/**
 * Class NotifySalesRepIdentity
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\Email\Container
 */
class NotifySalesRepIdentity extends \Cart2Quote\Quotation\Model\Quote\Email\Container\AbstractQuoteIdentity
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'cart2quote_email/notify_salesrep/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'cart2quote_email/notify_salesrep/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'cart2quote_email/notify_salesrep/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'cart2quote_email/notify_salesrep/template';
    const XML_PATH_EMAIL_ENABLED = 'cart2quote_email/notify_salesrep/enabled';

    /**
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * QuoteProposalAcceptedIdentity constructor.
     *
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($scopeConfig, $storeManager);
        $this->senderResolver = $senderResolver;
    }

    /**
     * @return string
     */
    public function getRecieverEmail()
    {
        $emailIdentity = $this->senderResolver->resolve($this->getEmailIdentity());

        return $emailIdentity['email'];
    }

    /**
     * @return string
     */
    public function getRecieverName()
    {
        $emailIdentity = $this->senderResolver->resolve($this->getEmailIdentity());

        return $emailIdentity['name'];
    }
}
