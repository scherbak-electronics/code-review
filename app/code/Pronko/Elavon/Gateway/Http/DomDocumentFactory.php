<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Http;

/**
 * DOM document factory
 */
class DomDocumentFactory
{
    /**
     * @param string $version
     * @param string $encoding
     * @return \DOMDocument
     */
    public function create($version = '1.0', $encoding = 'UTF-8')
    {
        return new \DOMDocument($version, $encoding);
    }
}
