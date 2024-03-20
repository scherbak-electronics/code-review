<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */
namespace Pronko\Elavon\Gateway\Http\Converter;
use Pronko\Elavon\Gateway\Http\DomDocumentFactory;
/**
 * Class Xml
 * @package     Pronko\Elavon\Gateway\Http\Converter
 */
class Xml
{
    const NAME = '_name';
    const VALUE = '_value';
    const ATTRIBUTE = '_attribute';
    /**
     * This value is used to replace numeric keys while formatting data for xml output.
     */
    const DEFAULT_ENTITY_ITEM_NAME = 'item';
    /**
     * @var \DOMDocument|null
     */
    private $dom;
    /**
     * @var \DOMDocument
     */
    private $currentDom;
    /**
     * @var DomDocumentFactory
     */
    private $domDocumentFactory;
    /**
     * Xml constructor.
     * @param DomDocumentFactory $domDocumentFactory
     */
    public function __construct(DomDocumentFactory $domDocumentFactory)
    {
        $this->domDocumentFactory = $domDocumentFactory;
    }
    /**
     * @return \DOMDocument|null
     */
    public function getDom()
    {
        if ($this->dom == null) {
            $this->dom = $this->domDocumentFactory->create();
            $this->dom->formatOutput = true;
        }
        return $this->dom;
    }
    /**
     * @return \DOMDocument
     */
    private function getCurrentDom()
    {
        if ($this->currentDom == null) {
            $this->currentDom = $this->getDom();
        }
        return $this->currentDom;
    }
    /**
     * @param \DOMDocument|\DOMNode $node
     * @return $this
     */
    private function setCurrentDom($node)
    {
        $this->currentDom = $node;
        return $this;
    }
    /**
     * @param array $content
     * @return $this
     * @throws \DOMException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function arrayToXml($content)
    {
        if (empty($content)) {
            return $this;
        }
        $parentNode = $this->getCurrentDom();
        foreach ($content as $key => $item) {
            $name = isset($item[self::NAME]) ? $item[self::NAME] : $key;
            $node = $this->getDom()->createElement(preg_replace('/[^\w-]/i', '', $name));
            $parentNode->appendChild($node);
            $this->processItem($item, $node);
        }
        return $this;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDom()->saveXML();
    }
    /**
     * @param array|string $item
     * @param \DOMElement $node
     * @return void
     */
    private function processItem($item, $node)
    {
        if (is_string($item)) {
            $text = $this->getDom()->createTextNode($item);
            $node->appendChild($text);
        } elseif (is_array($item)) {
            if (isset($item[self::ATTRIBUTE])) {
                $this->processItemValue($item, $node);
            } elseif (isset($item[0])) {
                foreach ($item as $v) {
                    $this->setCurrentDom($node)->arrayToXml(['item' => $v]);
                }
            } else {
                $this->setCurrentDom($node)->arrayToXml($item);
            }
        }
    }
    /**
     * @param array $item
     * @param \DOMElement $node
     * @return void
     */
    private function processItemValue($item, $node)
    {
        $value = isset($item[self::VALUE]) ? $item[self::VALUE] : '';
        if (is_array($value)) {
            if (isset($value[0])) {
                foreach ($value as $valueItem) {
                    $this->setCurrentDom($node)->arrayToXml($valueItem);
                }
            } else {
                $this->setCurrentDom($node)->arrayToXml($value);
            }
        } else {
            $child = $this->getDom()->createTextNode($value);
            $node->appendChild($child);
        }
        foreach ($item[self::ATTRIBUTE] as $attributeKey => $attributeValue) {
            $node->setAttribute($attributeKey, $attributeValue);
        }
    }
}