<?php
/**
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\SalesRep\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Cart2Quote\SalesRep\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->addSalesRepTypesTable($installer);
        $this->addSalesRepTable($installer);
        $installer->endSetup();
    }

    /**
     * Add the 'salesrep_user' Table
     *
     * @param \Magento\Framework\Setup\SetupInterface $installer
     * @return void
     */
    private function addSalesRepTable($installer)
    {
        /**
         * Create table 'salesrep_user'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('salesrep_user')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Admin user'
        )->addColumn(
            'object_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Linked Object'
        )->addColumn(
            'type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'SalesRep Type'
        )->addColumn(
            'is_main',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            [],
            'Is main user'
        )->addColumn(
            'created',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created date'
        )->addColumn(
            'updated',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Last updated date'
        )->addIndex(
            $installer->getIdxName('salesrep_user', ['user_id', 'object_id', 'type_id']),
            ['user_id', 'object_id', 'type_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName('salesrep_user', 'user_id', 'admin_user', 'user_id'),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
        )->addForeignKey(
            $installer->getFkName('salesrep_user', 'type_id', 'salesrep_type', 'type_id'),
            'type_id',
            $installer->getTable('salesrep_type'),
            'type_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
        )->setComment(
            'SalesRep Quotation Table'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Add the 'salesrep_type' Table
     *
     * @param \Magento\Framework\Setup\SetupInterface $installer
     * @return void
     */
    private function addSalesRepTypesTable($installer)
    {
        /**
         * Create table 'salesrep_type'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('salesrep_type')
        )->addColumn(
            'type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Linked Table Name'
        )->addColumn(
            'deleted',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'deleted flag'
        )->setComment(
            'SalesRep Ticket Table'
        );

        $installer->getConnection()->createTable($table);
    }
}
