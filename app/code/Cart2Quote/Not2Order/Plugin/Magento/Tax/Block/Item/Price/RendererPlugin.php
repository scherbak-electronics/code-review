<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Tax\Block\Item\Price;

/**
 * Class RendererPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Tax\Block\Item\Price
 */
class RendererPlugin extends \Cart2Quote\Not2Order\Plugin\BasePlugin
{
    /**
     * @param \Magento\Tax\Block\Item\Price\Renderer $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(\Magento\Tax\Block\Item\Price\Renderer $subject, $result)
    {
        $product = $subject->getItem()->getProduct();

        if ($product instanceof \Magento\Catalog\Model\Product) {
            if (!$this->dataHelper->showPrice($product, $this->dataHelper->getCustomerGroupId())) {
                return "";
            }
        }

        return $result;
    }
}