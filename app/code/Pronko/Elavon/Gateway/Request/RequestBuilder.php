<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Request\BuilderComposite;
use Pronko\Elavon\Gateway\Helper\SubjectReader;

/**
 * Class RequestBuilder
 * @package     Pronko\Elavon\Gateway\Request
 */
class RequestBuilder implements BuilderInterface
{
    /**#@+
     * Request node names constants
     */
    const TYPE = 'type';
    const TIMESTAMP = 'timestamp';
    const REQUEST = 'request';
    /**#@-*/

    /**
     * @var BuilderComposite
     */
    private $builderComposite;

    /**
     * @var string
     */
    private $type;

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * RequestBuilder constructor.
     * @param BuilderComposite $builderComposite
     * @param SubjectReader $requestSubjectReader
     * @param string $type
     */
    public function __construct(
        BuilderComposite $builderComposite,
        SubjectReader $requestSubjectReader,
        $type
    ) {
        $this->builderComposite = $builderComposite;
        $this->requestSubjectReader = $requestSubjectReader;
        $this->type = $type;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();

        $timestamp = date('YmdHis');
        $payment->setAdditionalInformation('timestamp', $timestamp);
        $payment->setAdditionalInformation('last_transaction_type', $this->type);

        return [
            'request' => [
                '_attribute' => [
                    self::TYPE => $this->type,
                    self::TIMESTAMP => $timestamp,
                ],
                '_value' => $this->builderComposite->build($buildSubject)
            ]
        ];
    }
}
