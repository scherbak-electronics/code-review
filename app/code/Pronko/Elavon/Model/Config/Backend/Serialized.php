<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Model\Config\Backend;

use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;

class Serialized extends \Magento\Framework\App\Config\Value
{
    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * Serialized constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        EncoderInterface $encoder,
        DecoderInterface $decoder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->encoder = $encoder;
        $this->decoder = $decoder;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _afterLoad() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            $this->setValue(empty($value) ? false : $this->decoder->decode($value));
        }
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
            $this->setValue($this->encoder->encode($this->getValue()));
        }
        parent::beforeSave();
        return $this;
    }
}
