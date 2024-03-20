<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\DeskMessageTemplate\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Cart2Quote\DeskMessageTemplate\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade schema action
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $messageTable = $setup->getTable('desk_message_template');

            if ($setup->tableExists($messageTable) == false) {
                $newTable = $setup->getConnection()->newTable(
                    $messageTable
                )->addColumn(
                    'message_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                    'Message ID'
                )->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Message Title'
                )->addColumn(
                    'is_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    6,
                    ['unsigned' => true, 'nullable' => false, 'identity' => false, 'default' => 1],
                    'Active Status'
                )->addColumn(
                    'content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Message Content'
                )->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Creation Time'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'Modification Time'
                )->setComment(
                    'SupportDesk Message Template'
                );
                $setup->getConnection()->createTable($newTable);
            }
        }
    }
}
