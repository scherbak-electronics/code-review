<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Model\Config;

use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Partial
 */
class Partial extends Value
{
    /**
     * Xml Path
     */
    const XML_PATH_CAN_REFUND_PARTIAL = 'payment/elavon/can_refund_partial_per_invoice';

    /**
     * @var ValueFactory
     */
    private $configValueFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function afterSave()
    {
        try {
            $this->configValueFactory->create()->load(
                self::XML_PATH_CAN_REFUND_PARTIAL,
                'path'
            )->setValue(
                $this->getValue()
            )->setPath(
                self::XML_PATH_CAN_REFUND_PARTIAL
            )->save();
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage());
            throw new LocalizedException(__('An error occurred during saving configuration.'));
        }
        return parent::afterSave();
    }
}
