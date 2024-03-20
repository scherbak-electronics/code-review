<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Spi;

interface FraudConfigInterface
{
    /**#@+
     * Config names constants
     */
    const FRAUD_FILTER_MODE = 'fraud_filter_mode';
    const IS_FRAUD_ENABLED = 'is_fraud_enabled';
    const PASSIVE = 'passive';
    const ACTIVE = 'active';
    const OFF = 'off';
    /**#@-*/

    /**
     * @return string
     */
    public function getFraudFilterMode();

    /**
     * @return bool
     */
    public function isFraudEnabled();

    /**
     * @return bool
     */
    public function isActiveFilter();
}
