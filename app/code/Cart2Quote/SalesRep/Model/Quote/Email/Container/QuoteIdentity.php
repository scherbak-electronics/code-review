<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Class QuoteIdentity
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
class QuoteIdentity extends AbstractQuoteIdentity implements SalesRepIdentityInterface
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_TO_SALESREP = 'quotation_email/new_quote_request/copy_to_salesrep';
    const XML_PATH_EMAIL_COPY_METHOD = 'quotation_email/new_quote_request/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'quotation_email/new_quote_request/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'quotation_email/new_quote_request/identity';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = 'quotation_email/new_quote_request/guest_template';
    const XML_PATH_EMAIL_TEMPLATE = 'quotation_email/new_quote_request/template';
    const XML_PATH_EMAIL_ENABLED = 'quotation_email/new_quote_request/enabled';

    /**
     * Return guest template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
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
