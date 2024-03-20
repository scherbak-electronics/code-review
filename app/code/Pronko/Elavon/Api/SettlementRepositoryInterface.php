<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Api;

/**
 * Interface SettlementRepositoryInterface
 * @package Pronko\Elavon\Api
 * @api
 */
interface SettlementRepositoryInterface
{
    /**
     * Loads a specified settlement entity
     *
     * @param int $entityId The settlement entity ID.
     * @return Data\SettlementInterface Settlement interface.
     */
    public function getById($entityId);

    /**
     * Deletes a specified payment token.
     *
     * @param Data\SettlementInterface $paymentToken The invoice.
     * @return bool
     */
    public function delete(Data\SettlementInterface $settlement);

    /**
     * Performs persist operations for a specified payment token.
     *
     * @param Data\SettlementInterface $paymentToken The payment token.
     * @return Data\SettlementInterface Payment token interface.
     */
    public function save(Data\SettlementInterface $settlement);

    /**
     * @return Data\SettlementInterface
     */
    public function createObject();
}
