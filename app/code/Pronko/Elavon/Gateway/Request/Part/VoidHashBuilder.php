<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Part;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Gateway\Helper\SubjectReader;
use Pronko\Elavon\Gateway\Helper\HashEncryptor;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class    HashBuilder
 * @package     Pronko\Elavon\Gateway\Request\Void
 */
class VoidHashBuilder implements BuilderInterface
{
    /**
     * Request node name
     */
    const HASH_TYPE = 'md5hash';

    /**
     * @var HashEncryptor
     */
    private $hashEncryptor;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * HashBuilder constructor.
     * @param HashEncryptor $hashEncryptor
     * @param SubjectReader $requestSubjectReader
     * @param ConfigInterface $config
     */
    public function __construct(
        HashEncryptor $hashEncryptor,
        SubjectReader $subjectReader,
        ConfigInterface $config
    ) {
        $this->hashEncryptor = $hashEncryptor;
        $this->subjectReader = $subjectReader;
        $this->config = $config;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        return [
            self::HASH_TYPE => $this->hashEncryptor->encrypt([
                $payment->getAdditionalInformation('timestamp'),
                $this->config->getMerchantId(),
                $this->config->getOrderPrefix() . $paymentDO->getOrder()->getOrderIncrementId(),
                '','',''
            ])
        ];
    }
}
