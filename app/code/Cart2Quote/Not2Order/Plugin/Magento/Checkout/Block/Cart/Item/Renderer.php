<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Checkout\Block\Cart\Item;

use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class Renderer
 * @package Cart2Quote\Not2Order\Plugin\Magento\Checkout\Block\Cart\Item
 */
class Renderer extends BasePlugin
{
    /**
     * Remove the price and price column from the quote and order page
     *
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetUnitPriceHtml($subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $this->parser->loadHtml($result);

        $xpath = '//span[@class="cart-price"]';
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $product = $subject->getProduct();

        $domNodeList = $this->parser->xpath($xpath);
        if ($domNodeList->length > 0) {
            foreach ($domNodeList as $domNode) {
                if (!$this->dataHelper->showPrice($product, $customerGroupId)) {
                    $domNode->parentNode->removeChild($domNode);
                }
            }

            $result = $this->parser->getHtml();
        }

        return $result;
    }

    /**
     * Remove the subtotal price and price column from the quote and order page
     *
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetRowTotalHtml($subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $this->parser->loadHtml($result);

        $xpath = '//span[@class="cart-price"]';
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $product = $subject->getProduct();
        $domNodeList = $this->parser->xpath($xpath);
        if ($domNodeList->length > 0) {
            foreach ($domNodeList as $domNode) {
                if (!$this->dataHelper->showPrice($product, $customerGroupId)) {
                    $domNode->parentNode->removeChild($domNode);
                }
            }

            $result = $this->parser->getHtml();
        }

        return $result;
    }
}
