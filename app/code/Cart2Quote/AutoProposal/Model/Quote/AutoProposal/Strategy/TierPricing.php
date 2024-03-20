<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy;

/**
 * Class TierPricing
 *
 * @package Cart2Quote\AutoProposal\Model\Quote\AutoProposal\Strategy
 */
class TierPricing extends AbstractStrategy
{
    /**
     * Strategy identifier
     */
    const STRATEGY_IDENTIFIER = 'tier_pricing';

    /**
     * @return $this
     */
    public function setProposalPrices()
    {
        //Don't have to set prices here as it takes the prices from Product tier pricing settings
        return $this;
    }
}
