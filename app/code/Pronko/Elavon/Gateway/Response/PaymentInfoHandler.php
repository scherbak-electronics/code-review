<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;

/**
 * Class PaymentInfoHandler
 */
class PaymentInfoHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private static $additionalInfoFields = [
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
     * CaptureHandler constructor.
     * @param SubjectReader $reader
     */
    public function __construct(
        SubjectReader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->reader->readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        foreach ($this->getTransactionDetails($response) as $key => $value) {
            $payment->setAdditionalInformation($key, $value);
        }

        $payment->setIsTransactionClosed(true);
        $payment->setTransactionId($response['pasref']);
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
            } elseif (in_array($key, self::$additionalInfoFields) && !empty($value)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
