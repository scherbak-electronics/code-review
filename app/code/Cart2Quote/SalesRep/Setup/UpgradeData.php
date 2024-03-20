<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @package Cart2Quote\SalesRep\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Cart2Quote\SalesRep\Setup\ReplaceConfigPaths
     */
    private $replaceConfigPaths;

    /**
     * UpgradeData constructor.
     *
     * @param \Cart2Quote\SalesRep\Setup\ReplaceConfigPaths $replaceConfigPaths
     */
    public function __construct(
        \Cart2Quote\SalesRep\Setup\ReplaceConfigPaths $replaceConfigPaths
    ) {
        $this->replaceConfigPaths = $replaceConfigPaths;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '1.1.7') < 0) {
            //replace old config path routes with the new paths
            $this->replaceConfigPaths->processConfigPaths();
        }
    }
}
