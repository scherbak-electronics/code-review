<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Converter\Amount;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Helper\HashEncryptor;
use Pronko\Elavon\Observer\DataAssignObserver;

/**
 * Class HashBuilder
 * @package     Pronko\Elavon\Gateway\Request\Part
 */
class HashBuilder implements BuilderInterface
{
    /**
     * Request node name
     */
    const HASH_TYPE = 'md5hash';

    /**
     * @var SubjectReader
     */
    private $reader;

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
     * @param SubjectReader $reader
     * @param ConfigInterface $config
     * @param Amount $amountConverter
     * @param HashEncryptor $hashEncryptor
     */
    public function __construct(
        SubjectReader $reader,
        ConfigInterface $config,
        Amount $amountConverter,
        HashEncryptor $hashEncryptor
    ) {
        $this->reader = $reader;
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
        return [
            self::HASH_TYPE => $this->hashEncryptor->encrypt($this->getHash($buildSubject))
        ];
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    private function getHash(array $buildSubject)
    {
        $paymentDO = $this->reader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $amount = $this->reader->readAmount($buildSubject);
        $order = $paymentDO->getOrder();

        return [
            $payment->getAdditionalInformation('timestamp'),
            $this->config->getMerchantId(),
            $this->config->getOrderPrefix() . $paymentDO->getOrder()->getOrderIncrementId(),
            $this->amountConverter->convert($amount, $order->getStoreId()),
            $order->getCurrencyCode(),
            $payment->getData(DataAssignObserver::CC_NUMBER)
        ];
    }
}
