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
declare(strict_types=1);

namespace Bss\GroupedProductOption\Plugin\Sales;

use Magento\Sales\Api\Data\OrderItemInterface;

class GetSkuFromOrderItem
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * GetSkuFromOrderItem constructor.
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param GetSkuFromOrderItemInterface $subject
     * @param callable $proceed
     * @param OrderItemInterface $orderItem
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(
        $subject,
        callable $proceed,
        OrderItemInterface $orderItem
    ): string {
        if ($orderItem->getProductType() !== 'grouped') {
            return $proceed($orderItem);
        }

        $product = $this->getProductById($orderItem->getData('product_id'));
        if ($product->getId()) {
            return $product->getSku();
        }
        return $proceed($orderItem);
    }

    /**
     * @param $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }
}
