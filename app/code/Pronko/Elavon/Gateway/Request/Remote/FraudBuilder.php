<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Remote;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\FraudConfigInterface;

/**
 * Class FraudBuilder
 * @package     Pronko\Elavon\Gateway\Request\Remote
 * @private
 */
class FraudBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const FRAUD_FILTER = 'fraudfilter';
    const FRAUD_FILTER_MODE = 'mode';
    const TSS_INFO = 'tssinfo';
    const ADDRESS = 'address';
    const SHIPPING = 'shipping';
    const BILLING = 'billing';
    const CODE = 'code';
    const TYPE = 'type';
    const COUNTRY = 'country';
    const CUSTOMER_IP_ADDRESS = 'custipaddress';
    /**#@-*/

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var FraudConfigInterface
     */
    private $config;

    /**
     * FraudBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param FraudConfigInterface $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        FraudConfigInterface $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!$this->config->isFraudEnabled()) {
            return [];
        }

        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $order = $paymentDO->getOrder();

        $billingAddress = $order->getBillingAddress();

        $result = [
            self::TSS_INFO => [
                'billing' => [
                    '_attribute' => [
                        self::TYPE => self::BILLING,
                    ],
                    '_value' => [
                        self::CODE => $billingAddress->getPostcode(),
                        self::COUNTRY => $billingAddress->getCountryId()
                    ],
                    '_name' => self::ADDRESS
                ],
                self::CUSTOMER_IP_ADDRESS => $order->getRemoteIp()
            ]
        ];

        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $result[self::TSS_INFO]['shipping'] = [
                '_attribute' => [
                    self::TYPE => self::SHIPPING,
                ],
                '_value' => [
                    self::CODE => $shippingAddress->getPostcode(),
                    self::COUNTRY => $shippingAddress->getCountryId()
                ],
                '_name' => self::ADDRESS
            ];
        }

        if (!$this->config->isActiveFilter()) {
            $result[self::FRAUD_FILTER] = [
                '_attribute' => [
                    self::FRAUD_FILTER_MODE => $this->config->getFraudFilterMode()
                ],
                '_value' => ''
            ];
        }

        return $result;
    }
}
