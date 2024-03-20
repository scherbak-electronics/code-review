<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class InstallData
 * @package Cart2Quote\Not2Order\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $hidePriceAttr = $eavSetup->getAttribute($entityTypeId, 'not2order_hide_price');

        if (!$hidePriceAttr) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'not2order_hide_price',
                [
                    'type' => 'int',
                    'label' => __('Hide Product Price'),
                    'input' => 'select',
                    'source' => 'Cart2Quote\Not2Order\Model\Config\YesNoUseConfig',
                    'global' => Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'group' => 'Product Details',
                    'default' => 2,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                ]
            );
        }

        $hideOrderAttr = $eavSetup->getAttribute($entityTypeId, 'not2order_hide_orderbtn');
        if (!$hideOrderAttr) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'not2order_hide_orderbtn',
                [
                    'type' => 'int',
                    'label' => __('Hide Order Button'),
                    'input' => 'select',
                    'source' => 'Cart2Quote\Not2Order\Model\Config\YesNoUseConfig',
                    'global' => Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'group' => 'Product Details',
                    'default' => 2,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                ]
            );
        }
    }
}
