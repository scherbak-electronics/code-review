<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Setup\Patch\Data;

/**
 * class InstallSalesRepTypeData
 * package Cart2Quote\SalesRep\Setup\Patch\Data
 */
class InstallSalesRepTypeData implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * This function inserts the SalesRep types
     *
     * @return InstallSalesRepTypeData|void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $table = $this->moduleDataSetup->getTable('salesrep_type');
        $columnData = $this->getDefaultSalesRepTypes();

        $this->moduleDataSetup->getConnection()->insertOnDuplicate(
            $table,
            $columnData
        );

        $this->moduleDataSetup->endSetup();
    }

    /**
     * Get the salesrep types
     *
     * @return array
     */
    private function getDefaultSalesRepTypes()
    {
        $columnData[] = ['type_id' => \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION, 'deleted' => 0];
        $columnData[] = ['type_id' => \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_TICKET, 'deleted' => 0];
        $columnData[] = ['type_id' => \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_ORDER, 'deleted' => 0];
        $columnData[] = ['type_id' => \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER, 'deleted' => 0];

        return $columnData;
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
}
