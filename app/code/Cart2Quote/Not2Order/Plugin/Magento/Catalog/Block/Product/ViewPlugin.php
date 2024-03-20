<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product;

use Magento\Catalog\Block\Product\View;
use Magento\Framework\App\ObjectManager;
use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class ViewPlugin
 *
 * @package Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product
 */
class ViewPlugin extends BasePlugin
{
    /**
     * Remove add to cart from product view.
     *
     * @param View $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(View $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $this->parser->loadHtml($result);
        $buttonId = $this->dataHelper->getAddToCartId();
        $xpath = sprintf('//button[@id="%s"]', $buttonId);
        $removeQty = false;
        $product = $subject->getProduct();
        $productTypeId = $product->getTypeId();
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $quotable = $this->dataHelper->isQuotable($product, $customerGroupId);
        $domNodeList = $this->parser->xpath($xpath);

        if ($domNodeList->length > 0) {
            $showCartButton = $this->dataHelper->hideOrderButton($product, $customerGroupId);
            $replace = $this->dataHelper->replaceButtonCheck();

            foreach ($domNodeList as $domNode) {
                if (!$showCartButton && !$replace && $productTypeId != 'configurable') {
                    $domNode->parentNode->removeChild($domNode);
                    $removeQty = true;
                } elseif (!$showCartButton && $replace) {
                    $replaceButton = $this->dataHelper->getReplaceButton();
                    $fragment = $this->parser->getDom()->createDocumentFragment();
                    $fragment->appendXML($replaceButton);
                    $domNode->parentNode->replaceChild($fragment, $domNode);
                    $removeQty = true;
                }

                if ($productTypeId == 'configurable') {
                    //remove price label
                    $xpathPrice = '//div[@class="price"]';
                    $domNodeList = $this->parser->xpath($xpathPrice);
                    if ($domNodeList->length > 0) {
                        foreach ($domNodeList as $domNode) {
                            $domNode->parentNode->removeChild($domNode);
                        }
                    }
                }
                if ($productTypeId != 'configurable') {
                    if (!$showCartButton) {
                        //remove instant-purchase
                        $xpathInstantPurchase = '//div[@id="instant-purchase"]';
                        $domNodeList = $this->parser->xpath($xpathInstantPurchase);
                        if ($domNodeList->length > 0) {
                            foreach ($domNodeList as $domNode) {
                                $domNode->parentNode->removeChild($domNode);
                            }
                        }

                        //remove paypall checkout
                        $xpathPaypallCheckout = '//div[contains(normalize-space(@class), "paypal") and contains(normalize-space(@class), "checkout")]';
                        $domNodeList = $this->parser->xpath($xpathPaypallCheckout);
                        if ($domNodeList->length > 0) {
                            foreach ($domNodeList as $domNode) {
                                $domNode->parentNode->removeChild($domNode);
                            }
                        }

                        //remove paypall express checkout
                        $xpathPaypallExpressCheckout = '//div[@id="paypal-smart-button"]';
                        $domNodeList = $this->parser->xpath($xpathPaypallExpressCheckout);
                        if ($domNodeList->length > 0) {
                            foreach ($domNodeList as $domNode) {
                                $domNode->parentNode->removeChild($domNode);
                            }
                        }

                        $instantDivClass = $this->dataHelper->getExtraInstantCheckoutDivClass();
                        if ($instantDivClass) {
                            $xpathInstantCheckout = '//div[contains(normalize-space(@class), "' . $instantDivClass . '")]';
                            $domNodeList = $this->parser->xpath($xpathInstantCheckout);
                            if ($domNodeList->length > 0) {
                                foreach ($domNodeList as $domNode) {
                                    $domNode->parentNode->removeChild($domNode);
                                }
                            }
                        }
                    }
                }
            }

            if ($removeQty) {
                $result = $this->parser->getHtml();
            }
        }

        $xpathQty = '//div[@class="field qty"]';
        $domNodeListQty = $this->parser->xpath($xpathQty);
        if ($domNodeListQty->length > 0) {
            foreach ($domNodeListQty as $domNodeQty) {
                if ($removeQty && !$quotable) {
                    $domNodeQty->parentNode->removeChild($domNodeQty);
                }
            }

            if ($removeQty) {
                $result = $this->parser->getHtml();
            }
        }

        return $result;
    }
}
