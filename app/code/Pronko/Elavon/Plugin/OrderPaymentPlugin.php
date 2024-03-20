<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Plugin;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Framework\Registry;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class PaymentPlugin
 * @package     Pronko\Elavon\Model\Plugin\Sales\Order
 */
class OrderPaymentPlugin
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $repository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * PaymentPlugin constructor.
     * @param TransactionRepositoryInterface $repository
     * @param Registry $registry
     */
    public function __construct(
        TransactionRepositoryInterface $repository,
        Registry $registry
    ) {
        $this->repository = $repository;
        $this->registry = $registry;
    }

    /**
     * Magento will consider a transaction for voiding only if it is an authorization
     * Elavon allows voiding capture transactions too
     *
     * Lookup an authorization transaction using parent transaction id, if set
     *
     * @param Payment $payment
     * @param \Closure $proceed
     * @return \Magento\Sales\Model\Order\Payment\Transaction|false
     */
    public function aroundGetAuthorizationTransaction(
        Payment $payment,
        \Closure $proceed
    ) {
        if ($payment->getMethodInstance()->getCode() != ConfigInterface::METHOD_CODE) {
            return $proceed();
        }
        $invoice = $this->registry->registry('current_invoice');
        if ($invoice && $invoice->getId()) {
            $transaction = $this->repository->getByTransactionType(
                Transaction::TYPE_CAPTURE,
                $payment->getId(),
                $payment->getOrder()->getId()
            );
            return $transaction ? $transaction : $proceed();
        }
        return $proceed();
    }
}
