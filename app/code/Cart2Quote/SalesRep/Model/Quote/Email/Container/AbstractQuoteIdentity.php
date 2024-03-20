<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\Quote\Email\Container;

/**
 * Class AbstractQuoteIdentity
 *
 * @package Cart2Quote\SalesRep\Model\Quote\Email\Container
 */
abstract class AbstractQuoteIdentity extends Container implements IdentityInterface
{
    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = '';
    const XML_PATH_EMAIL_COPY_TO = '';
    const XML_PATH_EMAIL_IDENTITY = '';
    const XML_PATH_EMAIL_TEMPLATE = '';
    const XML_PATH_EMAIL_ENABLED = '';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = '';

    /**
     * @var bool
     * This structure seems to be replaced by \Cart2Quote\SalesRep\Plugin\SenderBuilder::beforeSend
     */
    protected $sendCopyToSalesRep;

    /**
     * AbstractQuoteIdentity constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param bool $sendCopyToSalesRep
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $sendCopyToSalesRep = false
    ) {
        parent::__construct($scopeConfig, $storeManager);
        $this->sendCopyToSalesRep = $sendCopyToSalesRep;
    }

    /**
     * Get is enabled setting
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            $this::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue($this::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }

        return false;
    }

    /**
     * Return copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue($this::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue($this::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Get send copy to salesrep setting
     *
     * @return bool
     */
    public function isSendCopyToSalesRep()
    {
        return $this->sendCopyToSalesRep;
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue($this::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Get reciever email address
     *
     * @return string
     */
    public function getRecieverEmail()
    {
        return $this->getCustomerEmail();
    }

    /**
     * Get reciever name
     *
     * @return string
     */
    public function getRecieverName()
    {
        return $this->getCustomerName();
    }
}
