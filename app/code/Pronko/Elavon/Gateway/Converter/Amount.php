<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Converter;

use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Locale\FormatInterface;

/**
 * Class Amount
 * @package     Pronko\Elavon\Gateway\Converter
 */
class Amount
{
    /**
     * Default Precision
     */
    const DEFAULT_PRECISION = 2;

    /**
     * Amount Multiplier
     */
    const MULTIPLIER_BASE = 10;

    /**
     * @var FormatInterface
     */
    private $format;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver;

    /**
     * Amount constructor.
     * @param FormatInterface $format
     * @param ResolverInterface $localeResolver
     */
    public function __construct(FormatInterface $format, ResolverInterface $localeResolver)
    {
        $this->format = $format;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @param float|int $amount
     * @param int $storeId
     * @return int
     */
    public function convert($amount, $storeId = null)
    {
        $locale = $this->localeResolver->emulate($storeId);
        $priceFormat = $this->format->getPriceFormat($locale);
        $this->localeResolver->revert();

        $precision = self::DEFAULT_PRECISION;
        if (isset($priceFormat['precision'])) {
            $precision = $priceFormat['precision'];
        }

        $amount = $amount * pow(self::MULTIPLIER_BASE, $precision);

        return round($amount);
    }
}
