<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Spi;

use Magento\Payment\Gateway\ConfigInterface as PaymentConfigInterface;

/**
 * Interface ConfigInterface
 * @package Pronko\Elavon\Spi
 * @spi
 */
interface ConfigInterface extends PaymentConfigInterface
{
    const METHOD_CODE = 'elavon';
    const MODULE_NAME = 'Pronko_Elavon';
    const SS_ISSUE_NUMBER = 'ss_issue_number';
    const ORDER_PREFIX = 'order_prefix';
    const REFUND_PASSWORD = 'refund_password';
    const GATEWAY_URL = 'gateway_url';
    const GATEWAY_URL_SANDBOX = 'gateway_url_sandbox';
    const MERCHANT_ID = 'merchant_id';
    const ACCOUNT = 'account';
    const SECRET = 'secret';
    const USE_CVV = 'useccv';
    const SUB_ACCOUNTS = 'sub_accounts';
    const CAN_CAPTURE_PARTIAL = 'can_capture_partial';
    const ENVIRONMENT = 'environment';
    const PAYMENT_ACTION = 'payment_action';
    const REDIRECT_URL = 'redirect_url';
    const REDIRECT_URL_SANDBOX = 'redirect_url_sandbox';
    const CONNECTION_TYPE = 'connection_type';

    /**
     * @return string
     */
    public function getGatewayUrl();

    /**
     * @return string
     */
    public function getMerchantId();

    /**
     * @return string
     */
    public function getAccount();

    /**
     * @return mixed
     */
    public function getRefundPassword();

    /**
     * @return string
     */
    public function getModuleVersion();

    /**
     * @return mixed
     */
    public function getOrderPrefix();

    /**
     * @return mixed
     */
    public function getIsSsIssueNumber();

    /**
     * @return array
     */
    public function getSsStartYears();

    /**
     * @return mixed
     */
    public function getSecret();

    /**
     * @return mixed
     */
    public function getUseCvv();

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
