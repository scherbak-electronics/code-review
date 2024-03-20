<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Redirect;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Gateway\Helper\HashEncryptor;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Converter\Amount;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Spi\FraudConfigInterface;

/**
 * Class HashBuilder
 * @package Pronko\Elavon\Gateway\Request\Redirect
 * @private
 */
class HashBuilder implements BuilderInterface
{
    const HASH = 'sha1hash';

    /**
     * @var SubjectReader
     */
    private $reader;

    /**
     * @var Amount
     */
    private $amountConverter;

    /**
     * @var HashEncryptor
     */
    private $hashEncryptor;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var FraudConfigInterface
     */
    private $fraudConfig;

    /**
     * HashBuilder constructor.
     * @param SubjectReader $reader
     * @param Amount $amountConverter
     * @param HashEncryptor $hashEncryptor
     * @param ConfigInterface $config
     * @param FraudConfigInterface $fraudConfig
     */
    public function __construct(
        SubjectReader $reader,
        Amount $amountConverter,
        HashEncryptor $hashEncryptor,
        ConfigInterface $config,
        FraudConfigInterface $fraudConfig
    ) {
        $this->reader = $reader;
        $this->amountConverter = $amountConverter;
        $this->hashEncryptor = $hashEncryptor;
        $this->config = $config;
        $this->fraudConfig = $fraudConfig;
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

        $amount = $payment->getOrder()->getBaseTotalDue();
        $amount = $this->amountConverter->convert($amount, $payment->getOrder()->getStoreId());

        $hash = [
            $payment->getAdditionalInformation('timestamp'),
            $this->config->getMerchantId(),
            $this->config->getOrderPrefix() . $payment->getOrder()->getIncrementId(),
            $amount,
            $payment->getOrder()->getOrderCurrencyCode()
        ];
        
        $hash = sha1(implode('.', $hash));
        $hash = sha1($hash . '.' . $this->config->getValue('secret'));

        return [
            self::HASH => $hash
        ];
    }
}
