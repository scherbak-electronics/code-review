<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product\ProductList;

use Cart2Quote\Not2Order\Plugin\BasePlugin;
use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class RelatedPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product\ProductList
 */
class RelatedPlugin extends BasePlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * RelatedPlugin constructor.
     * @param \Cart2Quote\Not2Order\Html\Parser $parser
     * @param \Cart2Quote\Not2Order\Helper\Data $dataHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Cart2Quote\Not2Order\Html\Parser $parser,
        \Cart2Quote\Not2Order\Helper\Data $dataHelper,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($parser, $dataHelper);
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Related $subject
     * @param callable $proceed
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundCanItemsAddToCart(Related $subject, callable $proceed)
    {
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        foreach ($subject->getItems() as $item) {
            $productId = $item->getId();
            $product = $this->productRepository->getById($productId);
            $allowedOrder = $this->dataHelper->hideOrderButton($product, $customerGroupId);
            if ($allowedOrder && !$item->isComposite() && $item->isSaleable() && !$item->getRequiredOptions()) {
                return true;
            }
        }

        return false;
    }
}
