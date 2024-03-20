<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Helper;

/**
 * Class ScopeConfig
 * @package Cart2Quote\AutoProposal\Helper
 */
class ScopeConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * ScopeConfig constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Retrieve config value by path and scope.
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|string $scopeCode
     * @param bool $isSerialized
     * @return mixed
     */
    public function getValue(
        $path,
        $scopeType = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null,
        $isSerialized = false
    ) {
        $value = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);

        if ($isSerialized && $this->isSerialized($value)) {
            $value = unserialize($value);
        } else {
            $value = $this->jsonHelper->jsonDecode($value);
        }

        return $value;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isSerialized($value)
    {
        $unserializedValue = @unserialize($value);
        return $unserializedValue !== false || $value === 'b:0;';
    }
}