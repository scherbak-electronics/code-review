<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response\Remote;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class FraudHandler
 * @package Pronko\Elavon\Gateway\Response\Remote
 * @private
 */
class FraudHandler implements HandlerInterface
{
    /**#@+
     * Response field names constants
     */
    const FRAUD_RESPONSE = 'fraudresponse';
    const FRAUD_RESPONSE_MODE = 'mode';
    const FRAUD_RESPONSE_RESULT = 'result';
    const FRAUD_RESPONSE_NAME = 'name';
    const FRAUD_RESPONSE_ID = 'id';
    const FRAUD_RESPONSE_ACTION = 'action';
    const FRAUD_RESPONSE_RULES = 'rules';
    /**#@-*/

    /**#@+
     * Payment Additional Information field names constants
     */
    const FRAUD_FILTER_RULE_NAME = 'fraud_filter_rule_name';
    const FRAUD_FILTER_RESULT = 'fraud_filter_result';
    /**#@-*/

    /**
     * Execute action from HPP_FRAUDFILTER_RESULT
     */
    const FRAUDFILTER_RESULT_ACTIVE = 'ACTIVE';

    /**
     * DO not execute actions
     */
    const FRAUDFILTER_RESULT_PASSIVE = 'PASSIVE';

    /**
     * Disabled
     */
    const FRAUDFILTER_RESULT_OFF = 'OFF';

    /**
     * This is returned if the overall action is not available with that Acquiring Bank,
     * for example if the bank does not support the Hold state.
     */
    const FRAUDFILTER_RESULT_NOT_SUPPORTED = 'NOT SUPPORTED';

    /**
     * @var array
     */
    private static $resultModeList = ['HOLD', 'BLOCK'];

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * FraudHandler constructor.
     * @param SubjectReader $reader
     */
    public function __construct(
        SubjectReader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!array_key_exists(self::FRAUD_RESPONSE, $response)) {
            return;
        }

        /** @var Payment $payment */
        $payment = $this->reader->readPayment($handlingSubject)->getPayment();
        $fraudResponse = $response[self::FRAUD_RESPONSE];
        $mode = array_key_exists(self::FRAUD_RESPONSE_MODE, $fraudResponse)
            ? $fraudResponse[self::FRAUD_RESPONSE_MODE] : null;

        if ($mode == self::FRAUDFILTER_RESULT_OFF) {
            return;
        }

        if (!empty($fraudResponse)) {
            $result = $fraudResponse[self::FRAUD_RESPONSE_RESULT];
            if (in_array($result, self::$resultModeList) &&
                !in_array($mode, [self::FRAUDFILTER_RESULT_PASSIVE, self::FRAUDFILTER_RESULT_OFF])
            ) {
                $payment->setIsFraudDetected(true);
            }
            $payment->setAdditionalInformation(
                self::FRAUD_FILTER_RESULT,
                $result
            );
        }

        $executedRules = [];
        foreach ($fraudResponse[self::FRAUD_RESPONSE_RULES] as $ruleId => $rule) {
            $executedRules[$ruleId] = $rule['name'] . ' - ' . $rule['action'];
        }

        if (!empty($executedRules)) {
            $payment->setAdditionalInformation(
                self::FRAUD_FILTER_RULE_NAME,
                implode("\n", $executedRules)
            );
        }
    }
}
