<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\SalesRep\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @package Cart2Quote\SalesRep\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade schema action
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.7') < 0) {
            $tableName = $setup->getTable('salesrep_order');

            // Check if the table already exists
            if (!$setup->getConnection()->isTableExists($tableName)) {

                /**
                 * Create table 'salesrep_order'
                 */
                $table = $setup->getConnection()->newTable(
                    $tableName
                )->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'user_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable' => false],
                    'Admin user'
                )->addColumn(
                    'order',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable' => false],
                    'Order'
                )->addForeignKey(
                    $setup->getFkName('salesrep_order', 'user_id', 'admin_user', 'user_id'),
                    'user_id',
                    $setup->getTable('admin_user'),
                    'user_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
                );

                $setup->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '1.1.8') < 0) {
            $setup->getConnection()->addIndex(
                $setup->getTable('salesrep_user'),
                $setup->getIdxName('salesrep_user', ['object_id', 'type_id']),
                ['object_id', 'type_id']
            );
        }

        $setup->endSetup();
    }
}
