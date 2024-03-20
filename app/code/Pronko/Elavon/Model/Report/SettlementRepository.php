<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Model\Report;

use Pronko\Elavon\Api\SettlementRepositoryInterface;
use Pronko\Elavon\Api\Data\SettlementInterface;
use Pronko\Elavon\Model\ResourceModel\Report\Settlement as SettlementResourceModel;

/**
 * Class SettlementRepository
 * @package     Pronko\Elavon\Model\Report
 */
class SettlementRepository implements SettlementRepositoryInterface
{
    /**
     * @var SettlementResourceModel
     */
    private $resourceModel;

    /**
     * @var SettlementFactory
     */
    private $settlementFactory;

    /**
     * SettlementRepository constructor.
     * @param SettlementResourceModel $settlementResourceModel
     * @param SettlementFactory $settlementFactory
     */
    public function __construct(
        SettlementResourceModel $settlementResourceModel,
        SettlementFactory $settlementFactory
    ) {
        $this->resourceModel = $settlementResourceModel;
        $this->settlementFactory = $settlementFactory;
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function getById($entityId)
    {
        $entity = $this->settlementFactory->create();
        $this->resourceModel->load($entity, $entityId);
        return $entity;
    }

    /**
     * @return SettlementInterface
     */
    public function createObject()
    {
        return $this->settlementFactory->create();
    }

    /**
     * @param SettlementInterface $settlement
     * @return bool
     * @throws \Exception
     */
    public function delete(SettlementInterface $settlement)
    {
        $this->resourceModel->delete($settlement);
        return true;
    }

    /**
     * @param SettlementInterface $settlement
     * @return SettlementInterface
     * @throws \Exception
     */
    public function save(SettlementInterface $settlement)
    {
        $this->resourceModel->save($settlement);
        return $settlement;
    }
}
