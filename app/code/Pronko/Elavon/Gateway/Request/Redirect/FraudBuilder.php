<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Request\Redirect;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Pronko\Elavon\Spi\FraudConfigInterface;

/**
 * Class FraudBuilder
 * @package Pronko\Realex\Gateway\Request\Redirect
 * @private
 */
class FraudBuilder implements BuilderInterface
{
    /**#@+
     * Request names constants
     */
    const HPP_FRAUDFILTER_MODE = 'hpp_fraudfilter_mode';
    /**#@-*/

    /**
     * @var FraudConfigInterface
     */
    private $config;

    /**
     * FraudBuilder constructor.
     * @param FraudConfigInterface $config
     */
    public function __construct(
        FraudConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    // @codingStandardsIgnoreStart
    public function build(array $buildSubject)
    {// @codingStandardsIgnoreEnd
        if ($this->config->isFraudEnabled() && !$this->config->isActiveFilter()) {
            return [
                self::HPP_FRAUDFILTER_MODE => mb_strtoupper($this->config->getFraudFilterMode())
            ];
        }

        return [];
    }
}
