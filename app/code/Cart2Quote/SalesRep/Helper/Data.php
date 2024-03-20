<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Helper;

/**
 * Class Data
 * @package Cart2Quote\SalesRep\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Configuration Path to set SalesRep as the email sender
     */
    const XML_PATH_SET_SALESREP_AS_SENDER = 'quotation_email/global/set_salesrep_as_sender';

    /**
     * Path to limit view config
     */
    const XML_PATH_LIMIT_VIEW = 'cart2quote_salesrep/general/limit_view';

    /**
     * Path to admin user roles config
     */
    const XML_PATH_ADMIN_USER_ROLES = 'cart2quote_salesrep/general/admin_user_roles';

    /**
     *
     */
    const XML_PATH_AUTO_ASSIGNED_SALESREPS = 'cart2quote_salesrep/general/auto_assign';

    /**
     * Path to the salesrep 'auto assign' feature
     */
    const XML_PATH_ENABLE_AUTO_ASSIGN = 'cart2quote_salesrep/general/enable_auto_assign';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEmailSender()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SET_SALESREP_AS_SENDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isLimitViewEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LIMIT_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function isAssignSalesRepsEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_AUTO_ASSIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getExceptionGroup()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_USER_ROLES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return explode(",", $value);
    }

    /**
     * @return array
     */
    public function getAssignedSalesReps()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_AUTO_ASSIGNED_SALESREPS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($value !== null  && $this->isAssignSalesRepsEnabled()) {
            return explode(",", $value);
        }
    }
}
