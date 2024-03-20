<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Html;

/**
 * Class Parser
 * @package Cart2Quote\Not2Order\Html
 */
class Parser extends \Magento\Framework\Xml\Parser
{
    /**
     * @param string $string
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadHtml($string)
    {
        $this->initErrorHandler();

        if ($this->errorHandlerIsActive) {
            set_error_handler([$this, 'errorHandler']);
        }

        try {
            $html = '<not2order_parser_tag>' . $string . '</not2order_parser_tag>';

            $this->getDom()->loadHTML(
                mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            restore_error_handler();
            throw new \Magento\Framework\Exception\LocalizedException(
                __($exception->getMessage()),
                $exception
            );
        }

        if ($this->errorHandlerIsActive) {
            restore_error_handler();
        }

        return $this;
    }

    /**
     * Getter for the HTML witout the extra parser tag
     *
     * @return string|string[]
     */
    public function getHtml()
    {
        $html = $this->getDom()->saveHTML();
        $html = str_replace('<not2order_parser_tag>', '', $html);
        $html = str_replace('</not2order_parser_tag>', '', $html);

        return $html;
    }

    /**
     * @param $query
     * @return \DOMNodeList
     */
    public function xpath($query)
    {
        $domXpath = new \DOMXPath($this->getDom());
        return $domXpath->query($query);
    }

    /**
     * Custom XML lib error handler
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function errorHandler($errorNo, $errorStr, $errorFile, $errorLine)
    {
        //only trow real errors and ignore other types of errors
        if ($errorNo == 1) {
            $message = "{$errorStr} in {$errorFile} on line {$errorLine}";
            throw new \Magento\Framework\Exception\LocalizedException(new \Magento\Framework\Phrase($message));
        }
    }
}
