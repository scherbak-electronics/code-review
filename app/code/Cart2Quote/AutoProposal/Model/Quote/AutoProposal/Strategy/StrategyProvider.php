<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy;

/**
 * Class AutoProposalStrategyProvider
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
class StrategyProvider
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var array
     */
    private $strategyMap;

    /**
     * StrategyProvider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $strategyMap
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $strategyMap = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->strategyMap = $strategyMap;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * @return \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStrategy()
    {
        $type = $this->getScopeConfig()->getValue(
            \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyInterface::XML_CONFIG_PATH_AUTO_PROPOSAL_STRATEGY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!isset($this->strategyMap[$type]) ||
            !$this->strategyMap[$type] instanceof \Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy\StrategyInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Auto proposal strategy does not exist'));
        }

        return $this->strategyMap[$type];
    }
}
