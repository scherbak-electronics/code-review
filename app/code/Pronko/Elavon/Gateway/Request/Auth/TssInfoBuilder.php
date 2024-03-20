<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Auth;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class TssInfoBuilder
 * @package     Pronko\Elavon\Gateway\Request\Auth
 */
class TssInfoBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
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
    private $requestSubjectReader;

    /**
     * TssInfoBuilder constructor.
     * @param SubjectReader $requestSubjectReader
     */
    public function __construct(SubjectReader $requestSubjectReader)
    {
        $this->requestSubjectReader = $requestSubjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
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
        if ($order->getShippingAddress()) {
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
        return $result;
    }
}
