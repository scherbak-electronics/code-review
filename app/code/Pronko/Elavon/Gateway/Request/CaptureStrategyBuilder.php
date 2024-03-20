<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class CaptureStrategyBuilder
 * @package     Pronko\Elavon\Gateway\Request
 */
class CaptureStrategyBuilder implements BuilderInterface
{
    /**
     * @var BuilderInterface
     */
    private $captureBuilder;

    /**
     * @var BuilderInterface
     */
    private $settleBuilder;

    /**
     * @var BuilderInterface
     */
    private $multisettleBuilder;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CaptureStrategyBuilder constructor.
     * @param BuilderInterface $captureBuilder
     * @param BuilderInterface $settleBuilder
     * @param BuilderInterface $multisettleBuilder
     * @param SubjectReader $requestSubjectReader
     * @param ConfigInterface $config
     */
    public function __construct(
        BuilderInterface $captureBuilder,
        BuilderInterface $settleBuilder,
        BuilderInterface $multisettleBuilder,
        SubjectReader $requestSubjectReader,
        ConfigInterface $config
    ) {
        $this->captureBuilder = $captureBuilder;
        $this->settleBuilder = $settleBuilder;
        $this->multisettleBuilder = $multisettleBuilder;
        $this->requestSubjectReader = $requestSubjectReader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();

        $authTransaction = $payment->getAuthorizationTransaction();
        if ($authTransaction) {
            if ($this->config->canCapturePartial()) {
                return $this->multisettleBuilder->build($buildSubject);
            }
            return $this->settleBuilder->build($buildSubject);
        }

        return $this->captureBuilder->build($buildSubject);
    }
}
