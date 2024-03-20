<?php
/*
 * Copyright (c) 2023. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\SalesRep\Model\ResourceModel\Quote\Grid;

/**
 * Class Collection
 * @package Cart2Quote\SalesRep\Model\ResourceModel\Quote\Grid
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection
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
     * @param \Magento\Framework\DB\Helper $coreResourceHelper
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cart2Quote\SalesRep\Helper\Data $salesRepHelper,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = 'quotation_quote',
        $resourceModel = \Cart2Quote\Quotation\Model\ResourceModel\Quote::class
    ) {
        $this->authSession = $authSession;
        $this->salesRepHelper = $salesRepHelper;
        parent::__construct(
            $coreResourceHelper,
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @return $this|\Cart2Quote\SalesRep\Model\ResourceModel\Quote\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $connection = $this->getConnection();
        $adminUser = $this->authSession->getUser();
        $roleId = $adminUser->getRole()->getRoleId();
        $userId = $adminUser->getId();
        $quoteType = \Cart2Quote\SalesRep\Model\Type::SALES_REP_TYPE_QUOTATION;
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
            ['sru' => $this->getTable('salesrep_user')],
            'sru.object_id = main_table.quote_id AND sru.type_id = "' . $quoteType . '"',
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
