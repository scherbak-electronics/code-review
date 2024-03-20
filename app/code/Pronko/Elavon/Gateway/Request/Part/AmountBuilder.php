<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Converter\Amount;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AmountBuilder
 * @package     Pronko\Elavon\Gateway\Request\Part
 */
class AmountBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const AMOUNT = 'amount';
    const CURRENCY = 'currency';
    /**#@-*/

    /**
     * @var Amount
     */
    private $amountConverter;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * CommonBuilder constructor
     * @param Amount $amountConverter
     * @param SubjectReader $requestSubjectReader
     */
    public function __construct(
        Amount $amountConverter,
        SubjectReader $requestSubjectReader
    ) {
        $this->amountConverter = $amountConverter;
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
        $amount = $this->requestSubjectReader->readAmount($buildSubject);
        $paymentDO->getPayment()->setAdditionalInformation('last_amount', $amount);

        return [
            self::AMOUNT => [
                '_value' => $this->amountConverter->convert($amount, $order->getStoreId()),
                '_attribute' => [
                    self::CURRENCY => $order->getCurrencyCode()
                ]
            ]
        ];
    }
}
