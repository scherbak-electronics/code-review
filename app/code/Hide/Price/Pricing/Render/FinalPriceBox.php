<?php

namespace Hide\Price\Pricing\Render;

use Magento\Catalog\Pricing\Price;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Msrp\Pricing\Price\MsrpPrice;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{ 
    protected function wrapResult($html)
    {
        return '<div class="price-box ' . $this->getData('css_classes') . '" ' .
                'data-role="priceBox" ' .
                'data-product-id="' . $this->getSaleableItem()->getId() . '" ' .
                'data-price-box="product-id-' . $this->getSaleableItem()->getId() . '"' .
                '>' . $html . '</div>';
    }
}
