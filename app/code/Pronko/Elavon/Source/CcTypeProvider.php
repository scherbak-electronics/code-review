<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Source;

use Pronko\Elavon\Gateway\Converter\CardType as ElavonCcType;
use Magento\Payment\Model\Source\CctypeFactory;

/**
 * Class Elavon Payment CC Types Source Model
 */
class CcTypeProvider
{
    /**
     * @var ElavonCcType
     */
    private $config;

    /**
     * @var CctypeFactory
     */
    private $ccTypeFactory;

    /**
     * CcTypeProvider constructor.
     * @param ElavonCcType $ccType
     * @param CctypeFactory $ccTypeFactory
     */
    public function __construct(
        ElavonCcType $ccType,
        CctypeFactory $ccTypeFactory
    ) {
        $this->config = $ccType;
        $this->ccTypeFactory = $ccTypeFactory;
    }

    /**
     * Returns list of supported credit types
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        return $this->ccTypeFactory->create()->setAllowedTypes(
            $this->config->getTypes()
        )->toOptionArray();
    }
}
