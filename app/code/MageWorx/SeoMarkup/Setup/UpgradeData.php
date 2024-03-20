<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoMarkup\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $this->updateOpenGraphSettings($setup);
            $this->updateTwitterCardsSettings($setup);
            $this->duplicateProductDescriptionCodeSettingValue($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function updateOpenGraphSettings(ModuleDataSetupInterface $setup): void
    {
        $relatedSettingsPaths = [
            'mageworx_seo/markup/open_graph/enabled_for_product'   => 'mageworx_seo/markup/product/og_enabled',
            'mageworx_seo/markup/open_graph/enabled_for_category'  => 'mageworx_seo/markup/category/og_enabled',
            'mageworx_seo/markup/open_graph/enabled_for_page'      => 'mageworx_seo/markup/page/og_enabled',
            'mageworx_seo/markup/open_graph/enabled_for_home_page' => 'mageworx_seo/markup/website/og_enabled'
        ];

        foreach ($relatedSettingsPaths as $newPath => $oldPath) {
            $setup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['path' => $newPath],
                ['path = ?' => $oldPath]
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function updateTwitterCardsSettings(ModuleDataSetupInterface $setup): void
    {
        $relatedSettingsPaths = [
            'mageworx_seo/markup/tw_cards/username'              => 'mageworx_seo/markup/common/tw_username',
            'mageworx_seo/markup/tw_cards/enabled_for_product'   => 'mageworx_seo/markup/product/tw_enabled',
            'mageworx_seo/markup/tw_cards/enabled_for_category'  => 'mageworx_seo/markup/category/tw_enabled',
            'mageworx_seo/markup/tw_cards/enabled_for_page'      => 'mageworx_seo/markup/page/tw_enabled',
            'mageworx_seo/markup/tw_cards/enabled_for_home_page' => 'mageworx_seo/markup/website/tw_enabled',
        ];

        foreach ($relatedSettingsPaths as $newPath => $oldPath) {
            $setup->getConnection()->update(
                $setup->getTable('core_config_data'),
                ['path' => $newPath],
                ['path = ?' => $oldPath]
            );
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function duplicateProductDescriptionCodeSettingValue(ModuleDataSetupInterface $setup): void
    {
        $newPaths   = [
            'mageworx_seo/markup/open_graph/product_description_code',
            'mageworx_seo/markup/tw_cards/product_description_code'
        ];
        $connection = $setup->getConnection();
        $select     = $connection->select();
        $select
            ->from($setup->getTable('core_config_data'))
            ->where('path IN(?)', $newPaths);

        if ($connection->fetchOne($select)) {
            return;
        }

        $select = $connection->select();
        $select
            ->from($setup->getTable('core_config_data'), ['scope', 'scope_id', 'path', 'value'])
            ->where('path = ?', 'mageworx_seo/markup/product/description_code');

        $rows = $connection->fetchAll($select);
        $data = [];

        foreach ($rows as $row) {
            foreach ($newPaths as $path) {
                $row['path'] = $path;
                $data[]      = $row;
            }
        }

        if ($data) {
            $connection->insertMultiple($setup->getTable('core_config_data'), $data);
        }
    }
}
