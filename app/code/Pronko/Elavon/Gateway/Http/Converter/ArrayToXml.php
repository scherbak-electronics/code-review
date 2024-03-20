<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Http\Converter;

/**
 * Class ArrayToXml
 * @package     Pronko\Elavon\Gateway\Http\Converter
 */
class ArrayToXml
{
    /**
     * @var XmlFactory
     */
    private $xmlFactory;

    /**
     * ArrayToXml constructor.
     * @param XmlFactory $xmlFactory
     */
    public function __construct(XmlFactory $xmlFactory)
    {
        $this->xmlFactory = $xmlFactory;
    }

    /**
     * Converts request to a XML Request
     *
     * @param array $request
     * @return \DomDocument
     */
    public function convert(array $request)
    {
        return $this->xmlFactory->create()
            ->arrayToXml($request)
            ->getDom();
    }
}
