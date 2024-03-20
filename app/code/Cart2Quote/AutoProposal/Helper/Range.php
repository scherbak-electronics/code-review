<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Helper;

/**
 * Class Range
 *
 * @package Cart2Quote\AutoProposal\Helper
 */
class Range extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfig
     */
    private $scopeConfigHelper;
    /**
     * @var \Cart2Quote\AutoProposal\Model\RangeFactory
     */
    private $rangeFactory;

    /**
     * Range constructor.
     * @param \Cart2Quote\AutoProposal\Model\RangeFactory $rangeFactory
     * @param ScopeConfig $scopeConfigHelper
     * @param \Magento\Framework\App\Helper\Context $context
     * @internal param \Cart2Quote\AutoProposal\Model\RangeFactory $range
     */
    public function __construct(
        \Cart2Quote\AutoProposal\Model\RangeFactory $rangeFactory,
        \Cart2Quote\AutoProposal\Helper\ScopeConfig $scopeConfigHelper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfigHelper = $scopeConfigHelper;
        $this->rangeFactory = $rangeFactory;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     *
     * @return \Cart2Quote\AutoProposal\Model\Range|boolean
     */
    public function getCurrentRange(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $currentRange = false;
        foreach ($this->getRanges() as $range) {
            $subtotal = $quote->getSubtotal() * 1;
            $minValue = $range->getMinValue() * 1;
            $maxValue = $range->getMaxValue() * 1;

            if ($subtotal >= $minValue && $subtotal <= $maxValue || $subtotal >= $minValue && !$maxValue) {
                $currentRange = $range;
            }
        }

        return $currentRange;
    }

    /**
     * @return \Cart2Quote\AutoProposal\Model\Range[]
     */
    public function getRanges()
    {
        $value = $this->scopeConfigHelper->getValue(
            \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\Range::XML_CONFIG_PATH_AUTO_PROPOSAL_RANGES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            null,
            true
        );

        $ranges = [];
        if (is_array($value)) {
            foreach ($value as $range) {
                $ranges[] = $this->rangeFactory->create(['data' => $range]);
            }
        }

        return $ranges;
    }
}
