<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Model\Report;

use Magento\Framework\Model\AbstractModel;
use Pronko\Elavon\Api\Data\SettlementInterface;

/**
 * Class Settlement
 * @package     Pronko\Elavon\Model\Report
 */
class Settlement extends AbstractModel implements SettlementInterface
{
    const ACCOUNT = 'account';
    const ORDER_ID = 'order_id';
    const TRANSACTION_ID = 'transaction_id';
    const AMOUNT = 'amount';
    const CURRENCY = 'base_currency_code';
    const MESSAGE = 'message';
    const BATCH_ID = 'batch_id';
    const AUTH_CODE = 'auth_code';
    const CREATED_AT = 'created_at';
    const STATUS = 'status';
    const TXN_ID = 'txn_id';

    /**
     * Initialization
     */
    protected function _construct() // @codingStandardsIgnoreLine MEQP2.PHP.ProtectedClassMember.FoundProtected
    {
        $this->_eventPrefix = 'elavon_settlement';
        $this->_eventObject = 'elavon_settlement';
        $this->_idFieldName = 'txn_id';
        $this->_init(\Pronko\Elavon\Model\ResourceModel\Report\Settlement::class);
    }

    /**
     * @param int $txnId
     * @return $this
     */
    public function setId($txnId)
    {
        return $this->setData(self::TXN_ID, $txnId);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_getData(self::TXN_ID);
    }

    /**
     * @param string $account
     * @return $this
     */
    public function setAccount($account)
    {
        return $this->setData(self::ACCOUNT, $account);
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->_getData(self::ACCOUNT);
    }

    /**
     * @param $txnId
     * @return $this
     */
    public function setTransactionId($txnId)
    {
        return $this->setData(self::TRANSACTION_ID, $txnId);
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->_getData(self::TRANSACTION_ID);
    }

    /**
     * @param $orderIncrementId
     * @return $this
     */
    public function setOrderId($orderIncrementId)
    {
        return $this->setData(self::ORDER_ID, $orderIncrementId);
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->_getData(self::AMOUNT);
    }

    /**
     * @param $currencyIsoCode
     * @return $this
     */
    public function setCurrency($currencyIsoCode)
    {
        return $this->setData(self::CURRENCY, $currencyIsoCode);
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_getData(self::CURRENCY);
    }

    /**
     * @param $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @param $batchId
     * @return $this
     */
    public function setBatchId($batchId)
    {
        return $this->setData(self::BATCH_ID, $batchId);
    }

    /**
     * @return mixed
     */
    public function getBatchId()
    {
        return $this->_getData(self::BATCH_ID);
    }

    /**
     * @param $authCode
     * @return $this
     */
    public function setAuthCode($authCode)
    {
        return $this->setData(self::AUTH_CODE, $authCode);
    }

    /**
     * @return mixed
     */
    public function getAuthCode()
    {
        return $this->_getData(self::AUTH_CODE);
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_getData(self::MESSAGE);
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }
}
