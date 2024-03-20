<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Redirect;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class AddressBuilder
 */
class AddressBuilder implements BuilderInterface
{
    const BILLING_CODE = 'billing_code';
    const BILLING_CO = 'billing_co';
    const SHIPPING_CODE = 'shipping_code';
    const SHIPPING_CO = 'shipping_co';

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * AddressBuilder constructor.
     * @param SubjectReader $reader
     */
    public function __construct(SubjectReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->reader->readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();

        $result = [
            self::BILLING_CODE => $payment->getOrder()->getBillingAddress()->getPostcode(),
            self::BILLING_CO => $payment->getOrder()->getBillingAddress()->getCountryId(),
        ];
        $shippingAddress = $payment->getOrder()->getShippingAddress();
        if ($shippingAddress) {
            $result[self::SHIPPING_CODE] = $shippingAddress->getPostcode();
            $result[self::SHIPPING_CO] = $shippingAddress->getCountryId();
        }

        return $result;
    }
}
