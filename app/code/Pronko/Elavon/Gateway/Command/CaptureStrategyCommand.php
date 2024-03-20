<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\CommandInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class CaptureStrategyCommand
 */
class CaptureStrategyCommand implements CommandInterface
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CaptureStrategyCommand constructor.
     * @param CommandPoolInterface $commandPool
     * @param SubjectReader $reader
     * @param ConfigInterface $config
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        SubjectReader $reader,
        ConfigInterface $config
    ) {
        $this->commandPool = $commandPool;
        $this->reader = $reader;
        $this->config = $config;
    }

    /**
     * @param array $commandSubject
     * @return \Magento\Payment\Gateway\Command\ResultInterface|null
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = $this->reader->readPayment($commandSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();

        if ($payment->getAuthorizationTransaction()) {
            $command = $this->config->canCapturePartial() ?
                $this->commandPool->get('multisettle') :
                $this->commandPool->get('settle');
        } else {
            $command = $this->commandPool->get('capture_command');
        }

        return $command->execute($commandSubject);
    }
}
