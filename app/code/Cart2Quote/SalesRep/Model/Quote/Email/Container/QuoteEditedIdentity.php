<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Class QuoteEditedIdentity
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
class QuoteEditedIdentity extends AbstractQuoteIdentity implements SalesRepIdentityInterface
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_TO_SALESREP = 'quotation_email/quote_edited/copy_to_salesrep';
    const XML_PATH_EMAIL_COPY_METHOD = 'quotation_email/quote_edited/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'quotation_email/quote_edited/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'quotation_email/quote_edited/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'quotation_email/quote_edited/template';
    const XML_PATH_EMAIL_ENABLED = 'quotation_email/quote_edited/enabled';

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
