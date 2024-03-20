<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Cart2Quote\Desk\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $this->installStatuses($installer);
        $this->installPriorityType($installer);
    }

    /**
     * Install the statuses values
     *
     * @param ModuleDataSetupInterface $installer
     *
     * @return void
     */
    private function installStatuses(ModuleDataSetupInterface $installer)
    {
        $query = $installer->getConnection()
            ->query('SELECT * FROM ' . $installer->getTable('desk_ticket_status'));
        if ($query->rowCount() == 0) {
            $this->installTypes(
                $installer,
                $this->getStatusesValues(),
                $installer->getTable('desk_ticket_status')
            );
        }
    }

    /**
     * Install the priority types values
     *
     * @param ModuleDataSetupInterface $installer
     *
     * @return void
     */
    private function installPriorityType(ModuleDataSetupInterface $installer)
    {
        $query = $installer->getConnection()
            ->query('SELECT * FROM ' . $installer->getTable('desk_ticket_priority'));
        if ($query->rowCount() == 0) {
            $this->installTypes(
                $installer,
                $this->getPriorityValues(),
                $installer->getTable('desk_ticket_priority')
            );
        }
    }

    /**
     * Default install type function
     *
     * @param ModuleDataSetupInterface $installer
     * @param array $values
     * @param string $table
     *
     * @return void
     */
    private function installTypes(ModuleDataSetupInterface $installer, array $values, $table)
    {
        foreach ($values as $value) {
            $installer->getConnection()->insert($table, ['code' => $value]);
        }
    }

    /**
     * The priority values
     *
     * @return array
     */
    private function getPriorityValues()
    {
        return [
            \Cart2Quote\Desk\Model\Ticket\Priority::PRIORITY_LOW,
            \Cart2Quote\Desk\Model\Ticket\Priority::PRIORITY_NORMAL,
            \Cart2Quote\Desk\Model\Ticket\Priority::PRIORITY_HIGH,
            \Cart2Quote\Desk\Model\Ticket\Priority::PRIORITY_URGENT
        ];
    }

    /**
     * The status values
     *
     * @return array
     */
    private function getStatusesValues()
    {
        return [
            \Cart2Quote\Desk\Model\Ticket\Status::STATUS_OPEN,
            \Cart2Quote\Desk\Model\Ticket\Status::STATUS_PENDING,
            \Cart2Quote\Desk\Model\Ticket\Status::STATUS_SOLVED
        ];
    }
}
