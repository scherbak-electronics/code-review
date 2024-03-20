<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response\Transaction;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class AdditionalInfoHandler
 */
class AdditionalInfoHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private static $additionalInfoFields = [
        'merchantid',
        'account',
        'orderid',
        'message',
        'authcode',
        'result',
        'cvnresult',
        'avspostcoderesponse',
        'avsaddressresponse',
        'batchid',
        'pasref',
        'bank',
        'country',
        'countrycode',
        'region'
    ];

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * AdditionalInfoHandler constructor.
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
        $paymentDO = $this->reader->readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $rawDetailsInfo = $this->getTransactionDetails($response);
        foreach ($rawDetailsInfo as $key => $value) {
            $payment->setTransactionAdditionalInfo($key, $value);
        }
        $payment->setTransactionAdditionalInfo('raw_details_info', $rawDetailsInfo);
    }

    /**
     * @param array $response
     * @param array $result
     * @return array
     */
    private function getTransactionDetails(array $response, &$result = [])
    {
        foreach ($response as $key => $value) {
            if (is_array($value)) {
                $this->getTransactionDetails($value, $result);
            } elseif (in_array($key, self::$additionalInfoFields)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
