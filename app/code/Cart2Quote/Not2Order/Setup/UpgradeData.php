<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class UpgradeData
 * @package Cart2Quote\Not2Order\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * UpgradeData constructor.
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0') < 0) {
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(Product::ENTITY);

            //Change not2order_hide_price attribute values.
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'backend_type',
                'int'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'frontend_label',
                'Hide Price'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'frontend_input',
                'select'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'source_model',
                'Cart2Quote\Not2Order\Model\Config\YesNoUseConfig'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_global',
                Attribute::SCOPE_STORE
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_visible',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_required',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_user_defined',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'default_value',
                2
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_searchable',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_filterable',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_comparable',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_visible_on_front',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'used_in_product_listing',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_unique',
                false
            );

            //Change not2order_hide_orderbtn attribute values.
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'backend_type',
                'int'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'frontend_label',
                'Hide Order Button'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'frontend_input',
                'select'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'source_model',
                'Cart2Quote\Not2Order\Model\Config\YesNoUseConfig'
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_global',
                Attribute::SCOPE_STORE
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_visible',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_required',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_user_defined',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'default_value',
                2
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_searchable',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_filterable',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_comparable',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_visible_on_front',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'used_in_product_listing',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_unique',
                false
            );

            //Delete groups attribute from products
            $catalogSetup->removeAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn_groups'
            );

            $catalogSetup->removeAttribute(
                $entityTypeId,
                'not2order_hide_price_groups'
            );
        }

        /*if (version_compare($context->getVersion(), '2.0.3')) {
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(Product::ENTITY);
            // hide attributes from product info
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'is_visible_on_front',
                false
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'is_visible_on_front',
                false
            );
        }*/

        // To Do: create a frontend_model for both attributes to display '' on the frontend
        if (version_compare($context->getVersion(), '2.1.3')) {
            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $catalogSetup->getEntityTypeId(Product::ENTITY);
            // hide attributes from product info
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_price',
                'visible_on_front',
                true
            );
            $catalogSetup->updateAttribute(
                $entityTypeId,
                'not2order_hide_orderbtn',
                'visible_on_front',
                true
            );
        }

        $setup->endSetup();
    }
}
