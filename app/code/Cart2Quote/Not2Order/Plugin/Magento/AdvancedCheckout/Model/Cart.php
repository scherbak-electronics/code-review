<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\AdvancedCheckout\Model;

use Cart2Quote\Not2Order\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\AdvancedCheckout\Model\Cart as AdvancedCheckoutCart;
use Magento\AdvancedCheckout\Helper\Data as AdvancedCheckoutData;

/**
 * Class Cart
 * @package Cart2Quote\Not2Order\Plugin\Magento\AdvancedCheckout\Model
 */
class Cart
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Cart constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param Data $dataHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Data $dataHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if product can be added to the cart from Order by Sku
     *
     * @param AdvancedCheckoutCart $subject
     * @param array $result
     * @return array
     */
    public function afterCheckItem(AdvancedCheckoutCart $subject, $result)
    {
        if (isset($result['id'])) {
            $customerGroupId = $this->dataHelper->getCustomerGroupId();
            $storeId = $this->storeManager->getStore()->getId();
            $productId = $result['id'];
            $product = $this->productRepository->getById($productId, false, $storeId);
            if ($product->getId()) {
                $allowed = $this->dataHelper->hideOrderButton($product, $customerGroupId);
                if (!$allowed) {
                    $result['code'] = AdvancedCheckoutData::ADD_ITEM_STATUS_FAILED_PERMISSIONS;
                }
            }
        }

        return $result;
    }
}
