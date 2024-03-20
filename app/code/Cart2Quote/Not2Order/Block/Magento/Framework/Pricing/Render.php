<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Block\Magento\Framework\Pricing;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Render\Layout;
use Magento\Framework\View\Element\Template;
use Cart2Quote\Not2Order\Helper\Data;

/**
 * Class Render
 * @package Cart2Quote\Not2Order\Block\Magento\Framework\Pricing
 */
class Render extends \Magento\Framework\Pricing\Render
{
    /**
     * path to replace price on Product View Page
     */
    const XML_PATH_PRICE_REPLACER = 'cart2quote_not2order/global/replace_price';
    /**
     * @var \Cart2Quote\Not2Order\Helper\Data
     */
    private $dataHelper;

    /**
     * Render constructor.
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param Template\Context $context
     * @param Layout $priceLayout
     * @param array $data
     */
    public function __construct(Data $dataHelper, Template\Context $context, Layout $priceLayout, array $data)
    {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $priceLayout, $data);
    }

    /**
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render($priceCode, SaleableInterface $saleableItem, array $arguments = [])
    {
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $priceReplace = $this->_scopeConfig->getValue(
            SELF::XML_PATH_PRICE_REPLACER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($this->dataHelper->showPrice($saleableItem, $customerGroupId)) {
            return parent::render($priceCode, $saleableItem, $arguments);
        } elseif ($saleableItem->getTypeId() == 'configurable') {
            $saleableItem->setPrice(null);
            $html = parent::render($priceCode, $saleableItem, $arguments);

            return $html;
        }

        if ($priceReplace) {
            if ($arguments['zone'] == 'item_view' || $arguments['zone'] == 'item_list') {
                if (isset($arguments['price_type_code']) && $arguments['price_type_code'] == 'final_price'
                    || isset($priceCode) && $priceCode == "final_price") {
                    {
                        $html = sprintf(
                            '<div class="price-box price-final_price" data-role="priceBox" data-product-id="%s" data-price-box="product-id-%s">%s</div>',
                            $saleableItem->getId(),
                            $saleableItem->getId(),
                            $this->escapeHtml($priceReplace)
                        );
                        return $html;
                    }
                }
            }
        }

        return '';
    }

    /**
     * @param AmountInterface $amount
     * @param PriceInterface $price
     * @param SaleableInterface|null $saleableItem
     * @param array $arguments
     * @return string
     */
    public function renderAmount(
        AmountInterface $amount,
        PriceInterface $price,
        SaleableInterface $saleableItem = null,
        array $arguments = []
    ) {
        $customerGroupId = $this->dataHelper->getCustomerGroupId();

        if ($this->dataHelper->showPrice($saleableItem, $customerGroupId) ||
            $saleableItem->getTypeId() == 'configurable') {
            return parent::renderAmount($amount, $price, $saleableItem, $arguments);
        } else {
            return '<div class="price-box"></div>';
        }
    }
}
