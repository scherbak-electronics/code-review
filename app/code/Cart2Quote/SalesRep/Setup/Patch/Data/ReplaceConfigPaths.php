<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Setup\Patch\Data;

/**
 * class ReplaceConfigPaths
 * package Cart2Quote\SalesRep\Setup\Patch\Data
 */
class ReplaceConfigPaths implements \Magento\Framework\Setup\Patch\DataPatchInterface, \Magento\Framework\Setup\Patch\PatchVersionInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var string
     */
    private $oldBasePath = 'cart2quote_email';

    /**
     * @var string
     */
    private $newBasePath = 'quotation_email';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @return string[]
     */
    private function getConfigPaths()
    {
        return [
            'global/set_salesrep_as_sender',
            'quote_request/copy_to_salesrep',
            'quote_proposal/copy_to_salesrep',
            'quote_proposal_accepted/copy_to_salesrep',
            'quote_proposal_expire/copy_to_salesrep',
            'quote_reminder/copy_to_salesrep'
        ];
    }

    /**
     * This function converts old config path notation to new config path notation
     *
     * @return ReplaceConfigPaths|void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        foreach ($this->getConfigPaths() as $configPath) {
            if(!$this->scopeConfig->getValue("{$this->newBasePath}/{$configPath}")) {
                $this->moduleDataSetup->getConnection()->update(
                    'core_config_data',
                    ['path' => "{$this->newBasePath}/{$configPath}"],
                    ['path = ?' => "{$this->oldBasePath}/{$configPath}"]
                );
            }
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getVersion()
    {
        return '1.1.7';
    }
}
