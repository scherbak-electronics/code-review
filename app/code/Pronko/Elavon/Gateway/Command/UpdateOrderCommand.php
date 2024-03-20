<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Source\PaymentAction;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Spi\FraudConfigInterface;

/**
 * Class UpdateOrderCommand
 */
class UpdateOrderCommand implements CommandInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var FraudConfigInterface
     */
    private $fraudConfig;

    /**
     * UpdateOrderCommand constructor.
     * @param ConfigInterface $config
     * @param OrderRepositoryInterface $orderRepository
     * @param SubjectReader $subjectReader
     * @param FraudConfigInterface $fraudConfig
     */
    public function __construct(
        ConfigInterface $config,
        OrderRepositoryInterface $orderRepository,
        SubjectReader $subjectReader,
        FraudConfigInterface $fraudConfig
    ) {
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->subjectReader = $subjectReader;
        $this->fraudConfig = $fraudConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        switch ($this->config->getPaymentAction()) {
            case PaymentAction::CAPTURE:
                $fraudDetection = $this->fraudConfig->isFraudEnabled() && $this->fraudConfig->isActiveFilter();
                $payment->registerCaptureNotification($payment->getOrder()->getBaseTotalDue(), $fraudDetection);
                break;
            case PaymentAction::AUTHORIZE:
                $payment->registerAuthorizationNotification($payment->getOrder()->getBaseTotalDue());
                break;
        }

        $this->orderRepository->save($payment->getOrder());
    }
}
