<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

/**
 * Class CancelCommand
 */
class CancelCommand implements CommandInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * CancelCommand constructor.
     * @param OrderManagementInterface $orderManagement
     * @param Session $checkoutSession
     * @param SubjectReader $reader
     */
    public function __construct(
        OrderManagementInterface $orderManagement,
        Session $checkoutSession,
        SubjectReader $reader
    ) {
        $this->orderManagement = $orderManagement;
        $this->checkoutSession = $checkoutSession;
        $this->reader = $reader;
    }

    /**
     * Executes command basing on business object
     *
     * @param array $commandSubject
     * @return array
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $this->reader->readPayment($commandSubject);
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof Payment) {
            throw new \LogicException('Payment was not set.');
        }

        $this->orderManagement->cancel($paymentDO->getOrder()->getId());

        $this->checkoutSession->setLastRealOrderId($paymentDO->getOrder()->getOrderIncrementId());
        $this->checkoutSession->restoreQuote();

        return [];
    }
}
