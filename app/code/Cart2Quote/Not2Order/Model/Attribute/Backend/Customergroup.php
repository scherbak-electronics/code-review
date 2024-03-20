<?php
/**
 * ================================================================
 * Not2Order Model - Customergroup
 * ================================================================
 *
 * Render model that handels the customergroup data passed to and
 * retrieved by the product attribute field.
 *
 * Copyright © 2016 Cart2Quote. All rights reserved.
 * See LICENSE.txt for license details.
 *
 * @author     Rolf van der Kaaden <support@cart2quote.com>
 * @package    Not2Order
 * @copyright  Cart2Quote 2016
 */

namespace Cart2Quote\Not2Order\Model\Attribute\Backend;

/**
 * Class Customergroup
 *
 * @package Cart2Quote\Not2Order\Model\Attribute\Backend
 */
class Customergroup extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Construct
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Validate process
     * @param \Magento\Framework\DataObject $object
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate($object)
    {
        return true;
    }

    /**
     * Before Attribute Save Process
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = [];
            }
            $object->setData($attributeCode, join(',', $data));
        }
        if (!$object->hasData($attributeCode)) {
            $object->setData($attributeCode, false);
        }
        return $this;
    }

    /**
     * After Load Attribute Process
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if ($data) {
                $object->setData($attributeCode, explode(',', $data));
            }
        }
        return $this;
    }
}
