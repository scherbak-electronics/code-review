<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;

/**
 * Class TransactionBuilder
 * @package     Pronko\Elavon\Gateway\Request\Part
 */
class TransactionBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const PAS_REF = 'pasref';
    const AUTH_CODE = 'authcode';
    /**#@-*/

    /**
     * @var ManagerInterface
     */
    private $transactionManager;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CommonBuilder constructor.
     * @param ManagerInterface $transactionManager
     * @param SubjectReader $requestSubjectReader
     * @param ConfigInterface $config
     */
    public function __construct(
        ManagerInterface $transactionManager,
        SubjectReader $requestSubjectReader,
        ConfigInterface $config
    ) {
        $this->transactionManager = $transactionManager;
        $this->requestSubjectReader = $requestSubjectReader;
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $transaction = $this->getTransaction($payment);

        if (!$transaction instanceof TransactionInterface) {
            throw new LocalizedException(__('Transaction does not contain pasref or authcode to prepare request.'));
        }

        return [
            self::PAS_REF => $transaction->getAdditionalInformation('pasref'),
            self::AUTH_CODE => $transaction->getAdditionalInformation('authcode')
        ];
    }

    /**
     * @param InfoInterface $payment
     * @return false|Transaction
     */
    private function getTransaction(InfoInterface $payment)
    {
        $transaction = $this->transactionManager->getAuthorizationTransaction(
            null,
            $payment->getId(),
            $payment->getOrder()->getId()
        );

        if (!$transaction) {
            $transaction = $payment->getAuthorizationTransaction();
        }

        return $transaction;
    }
}
