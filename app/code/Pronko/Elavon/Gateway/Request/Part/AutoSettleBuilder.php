<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Source\PaymentAction;

/**
 * Class AutoSettleBuilder
 */
class AutoSettleBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const AUTO_SETTLE = 'autosettle';
    const FLAG = 'flag';
    const AUTHORIZE = 0;
    const CAPTURE = 1;
    const MULTIPLE = 'multi';
    /**#@-*/

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * AutoSettleBuilder constructor.
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
        $paymentDO->getPayment()->setAdditionalInformation(self::AUTO_SETTLE, $this->getAutoSettle());

        return [
            self::AUTO_SETTLE => [
                '_attribute' => [
                    self::FLAG => $this->getAutoSettle()
                ],
                '_value' => ''
            ]
        ];
    }

    /**
     * @return int
     */
    private function getAutoSettle()
    {
        return $this->config->getValue('payment_action') == PaymentAction::CAPTURE
            ? self::CAPTURE
            : ($this->config->getValue('can_capture_partial') == 1 ? self::MULTIPLE : self::AUTHORIZE);
    }
}
