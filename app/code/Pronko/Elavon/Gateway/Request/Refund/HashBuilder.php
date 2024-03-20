<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Refund;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Converter\Amount;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Helper\HashEncryptor;
use Pronko\Elavon\Gateway\Request\Part\AutoSettleBuilder;
use Pronko\Elavon\Observer\DataAssignObserver;

/**
 * Class HashBuilder
 * @package     Pronko\Elavon\Gateway\Request\Refund
 */
class HashBuilder implements BuilderInterface
{
    /**
     * Request node name
     */
    const ORDER_ID = 'orderid';
    const HASH_TYPE = 'md5hash';
    const REFUND_HASH_TYPE = 'refundhash';

    /**
     * @var SubjectReader
     */
    private $requestSubjectReader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Amount
     */
    private $amountConverter;

    /**
     * @var HashEncryptor
     */
    private $hashEncryptor;

    /**
     * HashBuilder constructor.
     * @param SubjectReader $requestSubjectReader
     * @param ConfigInterface $config
     * @param Amount $amountConverter
     * @param HashEncryptor $hashEncryptor
     */
    public function __construct(
        SubjectReader $requestSubjectReader,
        ConfigInterface $config,
        Amount $amountConverter,
        HashEncryptor $hashEncryptor
    ) {
        $this->requestSubjectReader = $requestSubjectReader;
        $this->config = $config;
        $this->amountConverter = $amountConverter;
        $this->hashEncryptor = $hashEncryptor;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->requestSubjectReader->readPayment($buildSubject);
        $amount = $this->requestSubjectReader->readAmount($buildSubject);
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $type = $paymentDO->getPayment()->getAdditionalInformation('autosettle');

        $orderId = ($type == AutoSettleBuilder::MULTIPLE) ? '_multisettle_' : '';
        $orderId .= $this->config->getOrderPrefix() . $order->getOrderIncrementId();

        return [
            self::ORDER_ID => $orderId,
            self::HASH_TYPE => $this->hashEncryptor->encrypt([
                $payment->getAdditionalInformation('timestamp'),
                $this->config->getMerchantId(),
                $orderId,
                $this->amountConverter->convert($amount, $order->getStoreId()),
                $order->getCurrencyCode(),
                $payment->getData(DataAssignObserver::CC_NUMBER)
            ]),
            self::REFUND_HASH_TYPE => sha1($this->config->getRefundPassword())
        ];
    }
}
