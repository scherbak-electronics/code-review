<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Response\Transaction;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Payment;

/**
 * Class AuthHandler
 */
class AuthHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * AuthHandler constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->reader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDataObject = $this->reader->readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDataObject->getPayment();

        $payment->setIsTransactionClosed(false);
        $payment->setTransactionId($response['pasref']);
    }
}
