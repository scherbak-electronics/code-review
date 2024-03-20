<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Converter;

/**
 * Class CardType
 */
class CardType
{
    /**
     * @var array
     */
    private $types;

    /**
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @param string $cardType
     * @return string
     */
    public function get($cardType)
    {
        if (isset($this->types[$cardType])) {
            return $this->types[$cardType];
        }

        return $cardType;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->types);
    }
}
