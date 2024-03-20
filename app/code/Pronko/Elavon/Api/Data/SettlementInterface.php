<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Api\Data;

/**
 * Interface SettlementInterface
 * @package Pronko\Elavon\Api\Data
 */
interface SettlementInterface
{
    /**
     * @return string
     */
    public function getAccount();

    /**
     * @param string $account
     * @return mixed
     */
    public function setAccount($account);

    /**
     * @param string $orderIncrementId
     * @return mixed
     */
    public function setOrderId($orderIncrementId);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $txnId
     * @return mixed
     */
    public function setTransactionId($txnId);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param float $amount
     * @return mixed
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param string $currency
     * @return mixed
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $message
     * @return mixed
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param $createdAt
     * @return mixed
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $batchId
     * @return mixed
     */
    public function setBatchId($batchId);

    /**
     * @return string
     */
    public function getBatchId();

    /**
     * @param string $authCode
     * @return mixed
     */
    public function setAuthCode($authCode);

    /**
     * @return string
     */
    public function getAuthCode();

    /**
     * @param string $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatus();
}
