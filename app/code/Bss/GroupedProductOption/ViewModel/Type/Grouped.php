<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GroupedProductOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GroupedProductOption\ViewModel\Type;

use Magento\Product\Model\Product;

class Grouped implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const ENABLE = 1;
    const DISABLE = 2;

    /**
     * Grouped constructor.
     * @param \Bss\GroupedProductOption\Helper\Integration $helper
     */
    public function __construct(
        \Bss\GroupedProductOption\Helper\Integration $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Retrieve form action
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->helper->getAjaxUrl();
    }

    /**
     * Get post action
     *
     * @param string $productId
     * @return string
     */
    public function getPostAction($productId)
    {
        return $this->helper->getPostAction($productId);
    }

    /**
     * Check is enable stock alert module.
     *
     * @param Product $product
     * @return bool
     */
    public function isStockAlertAllowed($product)
    {
        return $this->helper->isStockAlertAllowed() && !($product->getProductStockAlert() == self::DISABLE);
    }
}
