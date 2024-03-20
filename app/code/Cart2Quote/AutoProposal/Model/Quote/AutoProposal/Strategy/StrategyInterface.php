<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy;

/**
 * Interface StrategyInterface
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
interface StrategyInterface
{
    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_ENABLED = 'cart2quote_quotation/proposal/auto_proposal';
    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_STRATEGY = 'cart2quote_quotation/proposal/auto_proposal_strategy';
    /**
     * Config path
     */
    const XML_CONFIG_PATH_AUTO_PROPOSAL_DELAY = 'cart2quote_quotation/proposal/auto_proposal_delay';

    /**
     * Strategy identifier
     */
    const STRATEGY_IDENTIFIER = '';

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function propose(\Cart2Quote\Quotation\Model\Quote $quote = null);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return int
     */
    public function getDelayAmount();

    /**
     * @return $this
     */
    public function setProposalPrices();
}
