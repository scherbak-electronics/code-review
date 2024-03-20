<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Pronko\Elavon\Spi\ConfigInterface;

/**
 * Class Currency
 * @package     Pronko\Elavon\Gateway\Validator
 */
class Currency extends AbstractValidator
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Currency constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param ConfigInterface $config
     */
    public function __construct(ResultInterfaceFactory $resultFactory, ConfigInterface $config)
    {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['currency'])) {
            return $this->createResult(true);
        }
        $currencyCode = $validationSubject['currency'];

        $allowedCurrencies = explode(',', $this->config->getValue('allowed_currencies'));

        if (!$currencyCode || !in_array($currencyCode, $allowedCurrencies)) {
            return $this->createResult(false);
        }

        return $this->createResult(true);
    }
}
