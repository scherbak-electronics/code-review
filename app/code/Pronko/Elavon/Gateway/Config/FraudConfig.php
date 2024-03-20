<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
namespace Pronko\Elavon\Gateway\Config;

use Pronko\Elavon\Spi\FraudConfigInterface;

/**
 * Class FraudConfig
 */
class FraudConfig extends CommonConfig implements FraudConfigInterface
{
    /**
     * @return string
     */
    public function getFraudFilterMode()
    {
        return $this->getValue(self::FRAUD_FILTER_MODE);
    }

    /**
     * @return bool
     */
    public function isFraudEnabled()
    {
        return $this->getValue(self::IS_FRAUD_ENABLED);
    }

    /**
     * @return bool
     */
    public function isActiveFilter()
    {
        return $this->getValue(self::FRAUD_FILTER_MODE) === self::ACTIVE;
    }
}
