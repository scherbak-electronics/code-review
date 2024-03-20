<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Setup;

/**
 * Class ReplaceConfigPaths
 *
 * @package Cart2Quote\SalesRep\Setup
 */
class ReplaceConfigPaths extends \Cart2Quote\SalesRep\Setup\UpgradeData
{
    /**
     * New Configuration paths to replace pre Cart2Quote 4.2.0 paths
     *
     * @var array
     */
    public $newConfigPaths;

    /**
     * @var \Magento\Quote\Setup\QuoteSetup
     */
    protected $quoteSetup;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ReplaceConfigPaths constructor.
     *
     * @param \Magento\Quote\Setup\QuoteSetup $quoteSetup
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Quote\Setup\QuoteSetup $quoteSetup,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteSetup = $quoteSetup;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Replace old config path routes with the new paths
     */
    protected function processConfigPaths()
    {
        $connection = $this->quoteSetup->getConnection();
        $coreConfigTable = $this->quoteSetup->getTable('core_config_data');

        $newConfigPaths = [];
        $newConfigPaths["cart2quote_email/global/set_salesrep_as_sender"]                                          = "quotation_email/global/set_salesrep_as_sender";
        $newConfigPaths["cart2quote_email/quote_request/copy_to_salesrep"]                                         = "quotation_email/quote_request/copy_to_salesrep";
        $newConfigPaths["cart2quote_email/quote_proposal/copy_to_salesrep"]                                        = "quotation_email/quote_proposal/copy_to_salesrep";
        $newConfigPaths["cart2quote_email/quote_proposal_accepted/copy_to_salesrep"]                               = "quotation_email/quote_proposal_accepted/copy_to_salesrep";
        $newConfigPaths["cart2quote_email/quote_proposal_expire/copy_to_salesrep"]                                 = "quotation_email/quote_proposal_expire/copy_to_salesrep";
        $newConfigPaths["cart2quote_email/quote_reminder/copy_to_salesrep"]                                        = "quotation_email/quote_reminder/copy_to_salesrep";

        foreach ($newConfigPaths as $oldPath => $newPath) {
            if(!$this->scopeConfig->getValue($newPath)) {
                $connection->query("UPDATE {$coreConfigTable} SET `path` = REPLACE(`path`, '" . $oldPath . "', '" . $newPath . "') WHERE `path` = '" . $oldPath . "'");
            }
        }
    }
}
