<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Cart2Quote\Quotation\CustomerData;

use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class Quote
 * @package Cart2Quote\Not2Order\Plugin\Cart2Quote\Quotation\CustomerData
 */
class Quote extends BasePlugin
{
    /**
     * Remove the price and price column from the quote and order page
     *
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetSectionData($subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $items = $result['items'];
        if (is_array($items)) {
            foreach ($items as $id => $item) {
                if ($subject->getQuote()->getItems()) {
                    foreach ($subject->getQuote()->getItems() as $quoteItem) {
                        if ($quoteItem->getItemId() == $item['item_id']) {
                            $product = $quoteItem->getProduct();
                            if (!$this->dataHelper->showPrice($product, $customerGroupId)) {
                                $result['items'][$id]['product_price'] = '';
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }
}
