<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * Class InstallData
 * @package Cart2Quote\SalesRep\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Customer Setup Factory
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * Attribute Set Factory
     *
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * InstallData constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->installSalesRepTypes($setup);
    }

    /**
     * Install sales rep types
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function installSalesRepTypes(ModuleDataSetupInterface $setup)
    {
        foreach ($this->getDefaultSalesRepTypes() as $type) {
            $select = $setup->getConnection()
                ->select()
                ->from($setup->getTable('salesrep_type'))
                ->where('type_id = ?', $type);
            $result = $setup->getConnection()->fetchAll($select);

            if (count($result) == 0) {
                $setup->getConnection()->insert(
                    $setup->getTable('salesrep_type'),
                    ['type_id' => $type]
                );
            }
        }
    }

    /**
     * Get the salesrep types
     *
     * @return array
     */
    private function getDefaultSalesRepTypes()
    {
        return [
            \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION,
            \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_TICKET,
            \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_ORDER,
            \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_CUSTOMER
        ];
    }
}
