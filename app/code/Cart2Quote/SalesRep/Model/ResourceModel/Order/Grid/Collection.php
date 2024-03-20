<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Cart2Quote\SalesRep\Model\ResourceModel\Order\Grid
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * @var \Cart2Quote\SalesRep\Helper\Data
     */
    protected $salesRepHelper;

    /**
     * Collection constructor.
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Cart2Quote\SalesRep\Helper\Data $salesRepHelper
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cart2Quote\SalesRep\Helper\Data $salesRepHelper,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        $this->authSession = $authSession;
        $this->salesRepHelper = $salesRepHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return $this|\Cart2Quote\SalesRep\Model\ResourceModel\Quote\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $connection = $this->getConnection();
        $adminUser = $this->authSession->getUser();

        if (isset($adminUser)) {
            $roleId = $adminUser->getRole()->getRoleId();
            $userId = $adminUser->getId();
            $orderType = \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_ORDER;
            $adminUserTable = $this->getTable('admin_user');
            $salesRepColumns = [];
            $salesRepAliasName = 'salesrep';
            $columnNames = array_column($connection->describeTable($adminUserTable), 'COLUMN_NAME');

            foreach ($columnNames as $columnName) {
                $salesRepColumnAlias = "{$salesRepAliasName}_{$columnName}";
                $salesRepColumn = "{$salesRepAliasName}.{$columnName}";
                $salesRepColumns[$salesRepColumnAlias] = $salesRepColumn;
                $this->addFilterToMap($salesRepColumnAlias, $salesRepColumn);
            }

            $salesRepFullname = $connection->getConcatSql(
                ["{$salesRepAliasName}.firstname", "{$salesRepAliasName}.lastname"],
                ' '
            );

            $this->getSelect()->joinLeft(
                ['so' => $this->getTable('sales_order')],
                'so.entity_id = main_table.entity_id',
                ['quote_id'],
                null
            )->joinLeft(
                ['sru' => $this->getTable('salesrep_user')],
                'sru.object_id = so.quote_id AND sru.type_id = "' . $orderType . '"',
                ['user_id'],
                null
            )->joinLeft(
                [$salesRepAliasName => $adminUserTable],
                "sru.user_id = {$salesRepAliasName}.user_id",
                $salesRepColumns,
                null
            )->columns(
                [
                    "{$salesRepAliasName}_fullname" => $salesRepFullname,
                ]
            );

            if ($this->salesRepHelper->isLimitViewEnabled()) {
                $exclude = $this->salesRepHelper->getExceptionGroup();
                if (!in_array($roleId, $exclude)) {
                    $this->addFieldToFilter('salesrep_user_id', $userId);
                }
            }

            return $this;
        }
    }
}
