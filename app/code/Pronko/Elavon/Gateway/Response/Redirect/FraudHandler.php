<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response\Redirect;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class FraudHandler
 * @private
 */
class FraudHandler implements HandlerInterface
{
    /**#@+
     * Response field names constants
     */
    const HPP_FRAUDFILTER_MODE = 'hpp_fraudfilter_mode';
    const HPP_FRAUDFILTER_RESULT = 'hpp_fraudfilter_result';
    const HPP_FRAUDFILTER_RULE_PLACEHOLDER = 'hpp_fraudfilter_rule_';
    const HPP_FRAUDFILTER_RULE_NAME = 'hpp_fraudfilter_rule_name';
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
    public function __construct(SubjectReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!array_key_exists(self::HPP_FRAUDFILTER_RESULT, $response)) {
            return;
        }

        /** @var Payment $payment */
        $payment = $this->reader->readPayment($handlingSubject)->getPayment();

        if (array_key_exists(self::HPP_FRAUDFILTER_RESULT, $response)) {
            $mode = isset($response[self::HPP_FRAUDFILTER_MODE]) ? $response[self::HPP_FRAUDFILTER_MODE] : null;
            $result = $response[self::HPP_FRAUDFILTER_RESULT];
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

        if (array_key_exists(self::HPP_FRAUDFILTER_RULE_NAME, $response)) {
            $payment->setAdditionalInformation(
                self::FRAUD_FILTER_RULE_NAME,
                $response[self::HPP_FRAUDFILTER_RULE_NAME]
            );
        }

        $executedRules = [];
        foreach ($response as $fieldName => $value) {
            if (stripos($fieldName, self::HPP_FRAUDFILTER_RULE_PLACEHOLDER) === false) {
                continue;
            }
            $executedRules[$fieldName] = $value;
        }
    }
}
