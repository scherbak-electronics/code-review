<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Wishlist\Block\Customer\Wishlist;

use Magento\Wishlist\Block\Customer\Wishlist;
use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class ViewPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Wishlist\Block\Customer\Wishlist
 */
class ViewPlugin extends BasePlugin
{
    /**
     * Remove add to cart from wishlist.
     *
     * @param Wishlist $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(Wishlist $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $this->parser->loadHtml($result);

        $xpathToCart = '//div[@class="actions-primary"]/button[@data-role="tocart"]';
        $xpathAllToCart = '//div[@class="primary"]/button[@data-role="all-tocart"]';

        $domNodeListToCart = $this->parser->xpath($xpathToCart);
        $domNodeListAllToCart = $this->parser->xpath($xpathAllToCart);

        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $replace = $this->dataHelper->replaceButtonCheck();
        $replaceButton = $this->dataHelper->getReplaceButton();
        $hideAllButton = false;

        foreach ($domNodeListToCart as $domNodeToCart) {
            $form = $domNodeToCart->parentNode;
            foreach ($form->childNodes as $childNode) {
                if ($childNode->nodeName == 'button') {
                    $value = $childNode->getAttribute('data-item-id');
                    $item = $subject->getWishlistItems()->getItemById($value);
                    $actualProduct = $item->getProduct();
                    $show = $this->dataHelper->hideOrderButton($actualProduct, $customerGroupId);

                    if (!$show && !$replace) {
                        $form->removeChild($domNodeToCart);
                        $hideAllButton = true;
                    } elseif (!$show && $replace) {
                        $fragment = $this->parser->getDom()->createDocumentFragment();
                        $fragment->appendXML($replaceButton);
                        $form->replaceChild($fragment, $domNodeToCart);
                        $hideAllButton = true;
                    }
                }
            }
        }

        foreach ($domNodeListAllToCart as $domNodeAllToCart) {
            if ($domNodeListToCart->length > 0) {
                if ($hideAllButton) {
                    $domNodeAllToCart->parentNode->removeChild($domNodeAllToCart);
                }
            }
        }

        $result = $this->parser->getHtml();
        return $result;
    }
}
