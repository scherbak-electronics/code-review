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

namespace Bss\GroupedProductOption\Plugin\Wishlist;

class ItemAddToCart
{

    /**
     * Registry model.
     *
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Resolver.
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $resolver;

    /**
     * Product repository.
     *
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;

    /**
     * @var \Magento\Framework\Filter\LocalizedToNormalized
     */
    protected $localizedToNormalized;

    /**
     * ItemAddToCart constructor.
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     * @param \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
    ) {
        $this->registry = $registry;
        $this->resolver = $resolver;
        $this->productRepository = $productRepository;
        $this->localizedToNormalized = $localizedToNormalized;
    }

    /**
     * @param int $qty
     * @param array $params
     * @param int $id
     * @param mixed $product
     * @param mixed $cart
     * @return bool
     */
    private function addProductToCart($qty, $params, $id, $product, &$cart)
    {
        if ($this->checkQty($qty)) {
            return true;
        }
        $productChild = $this->productRepository->getById($id);
        $paramsChild = [];
        $paramsChild['uenc'] = $params['uenc'];
        $paramsChild['product'] = $id;
        $paramsChild['selected_configurable_option'] = $params['selected_configurable_option'];
        if ($this->checkOptionExist($params, $id)) {
            $paramsChild['options'] = $params['options_'.$id];
        }
        if ($this->checkGpoOptionExist($params, $id)) {
            foreach ($params['bss-gpo-option-'.$id] as $name => $value) {
                $paramsChild[$name] = $value;
            }
        }

        $paramsChild['qty'] = $qty;
        if (isset($paramsChild['qty'])) {
            $this->localizedToNormalized->setOptions(['locale' => $this->resolver->getLocale()]);
            $paramsChild['qty'] = $this->localizedToNormalized->filter($paramsChild['qty']);
        }

        if ($productChild->getTypeId() == 'configurable') {
            $paramsChild['super_attribute'] = $params['super_attribute'][$id];
        } else {
            $paramsChild['super_product_config'] = [
                'product_type' => $product->getTypeId(),
                'product_id' => $params['product']
            ];
        }

        /**
         * Check product availability
         */
        if (!$productChild) {
            return $this->goBack();
        }
        $this->registry->unregister('bss-gpo-group-add');
        $this->registry->register('bss-gpo-group-add', $id);
        $cart->addProduct($productChild, $paramsChild);
    }

    /**
     * @param int $qty
     * @return bool
     */
    private function checkQty($qty)
    {
        if (!isset($qty) || $qty <= 0 || $qty == '') {
            return true;
        }
        return false;
    }

    /**
     * @param array $params
     * @param int $id
     * @return bool
     */
    private function checkOptionExist($params, $id)
    {
        if (isset($params['options_'.$id]) && !empty($params['options_'.$id]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param array $params
     * @param int $id
     * @return bool
     */
    private function checkGpoOptionExist($params, $id)
    {
        if (isset($params['bss-gpo-option-'.$id]) && !empty($params['bss-gpo-option-'.$id]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param \Magento\Wishlist\Model\Item $subject
     * @param mixed $proceed
     * @param mixed $cart
     * @param bool $delete
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundAddToCart(
        \Magento\Wishlist\Model\Item $subject,
        $proceed,
        $cart,
        $delete = false
    ) {
        $product = $subject->getProduct();
        if ($product->getTypeId() === 'grouped') {
            $params = $subject->getBuyRequest()->getData();
            if (!isset($params['bss-gpo'])) {
                $flag = false;
                foreach ($product->getTypeInstance()->getAssociatedProducts($product) as $child) {
                    if ($child->getRequiredOptions()) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("Please specify product's required option(s).")
                    );
                }
            } else {

                foreach ($params['super_group'] as $id => $qty) {
                    // add product to cart
                    $this->addProductToCart($qty, $params, $id, $product, $cart);
                }

                if ($delete) {
                    $subject->delete();
                }
                return true;
            }
        }
        return $proceed($cart, $delete);
    }
}
