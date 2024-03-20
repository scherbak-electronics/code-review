<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\CustomerData;

use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\App\ViewInterface;
use Magento\Wishlist\Helper\Data as WishData;
use Magento\Wishlist\Model\Item;
use Cart2Quote\Not2Order\Helper\Data;

/**
 * Class Wishlist
 * @package Cart2Quote\Not2Order\CustomerData
 */
class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * Wishlist constructor.
     * @param Data $dataHelper
     * @param WishData $wishlistHelper
     * @param Sidebar $block
     * @param ImageFactory $imageHelperFactory
     * @param ViewInterface $view
     */
    public function __construct(
        Data $dataHelper,
        WishData $wishlistHelper,
        Sidebar $block,
        ImageFactory $imageHelperFactory,
        ViewInterface $view
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($wishlistHelper, $block, $imageHelperFactory, $view);
    }

    /**
     * Retrieve wishlist item data
     * Hides add to cart button wishlist sidebar.
     *
     * @param Item $wishlistItem
     * @return array
     */
    protected function getItemData(Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        return [
            'image' => $this->getImageData($product),
            'product_url' => $this->wishlistHelper->getProductUrl($wishlistItem),
            'product_name' => $product->getName(),
            'product_price' => $this->block->getProductPriceHtml(
                $product,
                'wishlist_configured_price',
                \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                ['item' => $wishlistItem]
            ),
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility() &&
                $this->dataHelper->hideOrderButton($product, $customerGroupId),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->wishlistHelper->getAddToCartParams($wishlistItem, true),
            'delete_item_params' => $this->wishlistHelper->getRemoveParams($wishlistItem, true),
        ];
    }
}
