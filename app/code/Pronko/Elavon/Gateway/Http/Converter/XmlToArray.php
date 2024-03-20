<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Http\Converter;

use Magento\Payment\Gateway\Http\ConverterInterface;
use Pronko\Elavon\Gateway\Http\DomDocumentFactory;

/**
 * Class XmlConverter
 * @package Pronko\Elavon\Gateway\Http
 */
class XmlToArray implements ConverterInterface
{
    /**
     * @var DomDocumentFactory
     */
    private $domDocumentFactory;

    /**
     * XmlConverter constructor.
     * @param DomDocumentFactory $domDocumentFactory
     */
    public function __construct(DomDocumentFactory $domDocumentFactory)
    {
        $this->domDocumentFactory = $domDocumentFactory;
    }

    /**
     * @inheritdoc
     */
    public function convert($xml)
    {
        $document = $this->domDocumentFactory->create();
        $document->loadXML($xml);

        return $this->toArray($document->documentElement);
    }

    /**
     * @param \DOMElement $element
     * @return array|string
     */
    private function toArray(\DOMElement $element)
    {
        $result = [];
        foreach ($element->attributes as $attrName => $attribute) {
            /** @var \DOMAttr $attribute */
            $result[$attrName] = $attribute->nodeValue;
        }
        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $childNode) {
                /** @var \DOMNode $childNode */
                switch ($childNode->nodeType) {
                    case XML_CDATA_SECTION_NODE:
                        $result['value'] = $childNode->textContent;
                        break;
                    case XML_TEXT_NODE:
                        $value = trim($childNode->nodeValue);
                        if ($value) {
                            $result = $value;
                        }
                        break;
                    case XML_ELEMENT_NODE:
                        /** @var \DOMElement $childNode */
                        $name = $this->getElementName($childNode);
                        $result[$name] = $this->toArray($childNode);
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * @param \DOMElement $childNode
     * @return string
     */
    private function getElementName($childNode)
    {
        if ($childNode->getAttribute('type')) {
            return $childNode->getAttribute('type');
        } elseif ($childNode->getAttribute('id')) {
            return $childNode->nodeName . $childNode->getAttribute('id');
        } elseif ($childNode->getAttribute('currency')) {
            return $childNode->getAttribute('currency');
        }
        return $childNode->nodeName;
    }
}
