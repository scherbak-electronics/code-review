<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class OrderIdBuilder
 * @package     Pronko\Elavon\Gateway\Request\Part
 */
class OrderIdBuilder implements BuilderInterface
{
    /**
     * Request node name
     */
    const ORDER_ID = 'orderid';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * CommonBuilder constructor.
     * @param ConfigInterface $config
     * @param SubjectReader $requestSubjectReader
     */
    public function __construct(
        ConfigInterface $config,
        SubjectReader $requestSubjectReader
    ) {
        $this->config = $config;
        $this->requestSubjectReader = $requestSubjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);

        return [
            self::ORDER_ID => $this->config->getOrderPrefix() . $paymentDO->getOrder()->getOrderIncrementId()
        ];
    }
}
