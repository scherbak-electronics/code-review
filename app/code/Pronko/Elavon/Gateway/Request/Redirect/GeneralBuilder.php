<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Redirect;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\ConfigInterface;
use Pronko\Elavon\Gateway\Converter\Amount;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Request\Version;
use Pronko\Elavon\Observer\DataAssignObserver;

/**
 * Class GeneralBuilder
 */
class GeneralBuilder implements BuilderInterface
{
    /**#@+
     * Request names constants
     */
    const ORDER_ID = 'order_id';
    const TIMESTAMP = 'timestamp';
    const MERCHANT_ID = 'merchant_id';
    const ACCOUNT = 'account';
    const AMOUNT = 'amount';
    const CURRENCY = 'currency';
    const AUTO_SETTLE_FLAG = 'auto_settle_flag';
    const COMMENT1 = 'comment1';
    const COMMENT2 = 'comment2';

    /**#@-*/

    /**
     * @var SubjectReader
     */
    private $reader;

    private $config;

    private $amountConverter;

    private $url;

    private $version;

    /**
     * FieldsBuilder constructor.
     * @param SubjectReader $reader
     * @param ConfigInterface $config
     * @param Amount $amountConverter
     * @param \Magento\Framework\UrlInterface $url
     * @param Version $version
     */
    public function __construct(
        SubjectReader $reader,
        ConfigInterface $config,
        Amount $amountConverter,
        \Magento\Framework\UrlInterface $url,
        Version $version
    ) {
        $this->reader = $reader;
        $this->config = $config;
        $this->amountConverter = $amountConverter;
        $this->url = $url;
        $this->version = $version;
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

        return [
            self::MERCHANT_ID => $this->config->getMerchantId(),
            self::ACCOUNT => $this->config->getSubAccount($payment->getData(DataAssignObserver::CC_TYPE)),
            self::ORDER_ID => $this->config->getOrderPrefix() . $payment->getOrder()->getIncrementId(),
            self::AMOUNT => $amount,
            self::CURRENCY => $payment->getOrder()->getOrderCurrencyCode(),
            self::TIMESTAMP => $payment->getAdditionalInformation('timestamp'),
            self::AUTO_SETTLE_FLAG => $payment->getAdditionalInformation('autosettle'),
            self::COMMENT1 => $this->version->getProductVersion(),
            self::COMMENT2 => $this->version->getVersion(),
            'return_tss' => 1,
            'merchant_response_url' => $this->url->getUrl($this->config->getValue('redirect_response_url')),
            'card_payment_button' => $this->config->getValue('card_payment_button_label'),
        ];
    }
}
