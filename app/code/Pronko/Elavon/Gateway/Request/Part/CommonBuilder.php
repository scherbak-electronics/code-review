<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Observer\DataAssignObserver;

/**
 * Class CommonBuilder
 */
class CommonBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const MERCHANT_ID = 'merchantid';
    const ACCOUNT = 'account';
    /**#@-*/

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * CommonBuilder constructor.
     * @param ConfigInterface $config
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        ConfigInterface $config,
        SubjectReader $subjectReader
    ) {
        $this->config = $config;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDataObject = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        return [
            self::MERCHANT_ID => $this->config->getMerchantId(),
            self::ACCOUNT => $this->config->getSubAccount($payment->getData(DataAssignObserver::CC_TYPE))
        ];
    }
}
