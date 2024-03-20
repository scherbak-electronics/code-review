<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Class QuoteProposalRejectedIdentity
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
class QuoteProposalRejectedIdentity extends AbstractQuoteIdentity implements SalesRepIdentityInterface
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_TO_SALESREP = 'quotation_email/quote_proposal_rejected/copy_to_salesrep';
    const XML_PATH_EMAIL_COPY_METHOD = 'quotation_email/quote_proposal_rejected/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'quotation_email/quote_proposal_rejected/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'quotation_email/quote_proposal_rejected/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'quotation_email/quote_proposal_rejected/template';
    const XML_PATH_EMAIL_ENABLED = 'quotation_email/quote_proposal_rejected/enabled';

    /**
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * QuoteProposalRejectedIdentity constructor.
     *
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($scopeConfig, $storeManager);
        $this->senderResolver = $senderResolver;
    }

    /**
     * Get reciever email
     *
     * @return string
     */
    public function getRecieverEmail()
    {
        $emailIdentity = $this->senderResolver->resolve($this->getEmailIdentity());
        return $emailIdentity['email'];
    }

    /**
     * Get reciever name
     *
     * @return string
     */
    public function getRecieverName()
    {
        $emailIdentity = $this->senderResolver->resolve($this->getEmailIdentity());
        return $emailIdentity['name'];
    }

    /**
     * @return bool
     */
    public function isSendCopyToAssignedSalesRep()
    {
        return $this->getConfigValue($this::XML_PATH_EMAIL_COPY_TO_SALESREP, $this->getStore()->getStoreId());
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->getConfigValue($this::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
