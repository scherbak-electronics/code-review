<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway;

use Magento\Payment\Gateway\ConfigInterface as PaymentConfigInterface;

/**
 * Interface ConfigInterface
 * @package Pronko\Elavon\Gateway
 * @deprecated since 2.1.0
 * @see \Pronko\Elavon\Spi\ConfigInterface
 */
interface ConfigInterface extends PaymentConfigInterface
{
    const METHOD_CODE = 'elavon';
    const MODULE_NAME = 'Pronko_Elavon';

    /**
     * @param null $storeId
     * @return string
     */
    public function getGatewayUrl($storeId = null);

    /**
     * @param null $storeId
     * @return string
     */
    public function getMerchantId($storeId = null);

    /**
     * @param null $storeId
     * @return string
     */
    public function getAccount($storeId = null);

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRefundPassword($storeId = null);

    /**
     * @return string
     */
    public function getModuleVersion();

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getOrderPrefix($storeId = null);

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getIsSsIssueNumber($storeId = null);

    /**
     * @return array
     */
    public function getSsStartYears();

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getSecret($storeId = null);

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getUseCvv($storeId = null);

    /**
     * @param string $type
     * @return string
     */
    public function getSubAccount($type);

    /**
     * @return bool
     */
    public function canCapturePartial();

    /**
     * @return string
     */
    public function getEnvironment();

    /**
     * @return string
     */
    public function getPaymentAction();

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @return int
     */
    public function getAutoSettle();

    /**
     * @return string
     */
    public function getConnectionType();
}
