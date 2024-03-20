<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Model\Order\Payment;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class InitializeCommand
 * @package Pronko\Elavon\Gateway\Command
 */
class InitializeCommand implements CommandInterface
{
    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * InitializeCommand constructor.
     * @param SubjectReader $reader
     */
    public function __construct(
        SubjectReader $reader,
        ConfigInterface $config
    ) {
        $this->reader = $reader;
        $this->config = $config;
    }

    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject)
    {
        $stateObject = $this->reader->readStateObject($commandSubject);
        $paymentDO = $this->reader->readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        $payment->setAmountAuthorized($payment->getOrder()->getTotalDue());
        $payment->setBaseAmountAuthorized($payment->getOrder()->getBaseTotalDue());
        $payment->getOrder()->setCanSendNewEmailFlag(false);

        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData('is_notified', false);

        $payment->setAdditionalInformation('timestamp', date('YmdHis'));
        $payment->setAdditionalInformation('autosettle', $this->config->getAutoSettle());
        $type = $this->config->getAutoSettle() ? 'capture' : 'auth';
        $payment->setAdditionalInformation('last_transaction_type', $type);
        $payment->setAdditionalInformation('last_amount', $payment->getOrder()->getBaseTotalDue());
    }
}
