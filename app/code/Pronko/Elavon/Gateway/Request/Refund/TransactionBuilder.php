<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Refund;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Request\Part\AutoSettleBuilder;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class TransactionBuilder
 * @package     Pronko\Elavon\Gateway\Request\Refund
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
     * @var ContextHelper
     */
    private $contextHelper;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * @var TransactionRepositoryInterface
     */
    private $repository;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CommonBuilder constructor.
     * @param ManagerInterface $transactionManager
     * @param SubjectReader $requestSubjectReader
     * @param TransactionRepositoryInterface $repository
     * @param ConfigInterface $config
     */
    public function __construct(
        ManagerInterface $transactionManager,
        SubjectReader $requestSubjectReader,
        TransactionRepositoryInterface $repository,
        ConfigInterface $config
    ) {
        $this->transactionManager = $transactionManager;
        $this->requestSubjectReader = $requestSubjectReader;
        $this->repository = $repository;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
        /** @var Payment $payment */
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
     * @return bool|false|Transaction
     */
    private function getTransaction(InfoInterface $payment)
    {
        $transaction = false;
        if ($payment->getAdditionalInformation('autosettle') === AutoSettleBuilder::MULTIPLE) {
            $refundTransactionId = $payment->getRefundTransactionId();
            if ($refundTransactionId) {
                $transaction = $this->repository->getByTransactionId(
                    $refundTransactionId,
                    $payment->getId(),
                    $payment->getOrder()->getId()
                );
            }
        }
        if (!$transaction) {
            $transaction = $this->transactionManager->getAuthorizationTransaction(
                null,
                $payment->getId(),
                $payment->getOrder()->getId()
            );

            if (!$transaction) {
                $transaction = $payment->getAuthorizationTransaction();
            }
        }

        return $transaction;
    }
}
