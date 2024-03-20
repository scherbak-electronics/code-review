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
namespace Bss\GroupedProductOption\Observer;

use \Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CheckResult implements ObserverInterface
{
    /**
     * Customer cart
     *
     * @var CustomerCart
     */
    private $cart;

    /**
     * Product repository
     *
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Resolver
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $locale;

    /**
     * @var \Magento\Framework\Filter\LocalizedToNormalized
     */
    protected $localizedToNormalized;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $locale,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized
    ) {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->locale = $locale;
        $this->localizedToNormalized = $localizedToNormalized;
    }

    /**
     * @param mixed $associatedProducts
     * @return bool
     */
    private function returnCheckRedirect($associatedProducts)
    {
        $redirect = false;
        if ($associatedProducts) {
            foreach ($associatedProducts as $associatedProduct) {
                if ($associatedProduct->getRequiredOptions()) {
                    $redirect = true;
                    break;
                }
            }
        }
        return $redirect;
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
     * @param int $qty
     * @param int $id
     * @param array $params
     * @param mixed $product
     * @param mixed $result
     * @param array $messages
     * @return bool
     */
    private function addProductToCart($qty, $id, $params, $product, &$result, &$messages)
    {
        if ($this->checkQty($qty)) {
            return true;
        }
        $productChild = $this->productRepository->getById($id);
        $paramsChild = [];
        $paramsChild['product'] = $id;

        $paramsChild['selected_configurable_option'] = $params['selected_configurable_option'];
        if (isset($params['options_'.$id]) && !empty($params['options_'.$id]) > 0) {
            $paramsChild['options'] = $params['options_'.$id];
        }

        if (isset($params['bss-gpo-option-'.$id]) && !empty($params['bss-gpo-option-'.$id]) > 0) {
            foreach ($params['bss-gpo-option-'.$id] as $name => $value) {
                $paramsChild[$name] = $value;
            }
        }

        $paramsChild['qty'] = $qty;
        if (isset($paramsChild['qty'])) {
            $this->localizedToNormalized->setOptions(['locale' => $this->locale->getLocale()]);
            $paramsChild['qty'] = $this->localizedToNormalized->filter($paramsChild['qty']);
        }
        $paramsChild['super_product_config'] = [
            'product_type' => $product->getTypeId(),
            'product_id' => $params['product']
        ];

        /**
         * Check product availability
         */
        if (!$productChild) {
            $result->setStatus(false);
            $messages[] = [
                'type' => 'error',
                'message' => __("Product $id not exist.")
            ];
        }
        if ($productChild->getTypeId() == 'grouped') {
            $paramsChild['simple_name'] = $productChild->getname();
            $paramsChild['simple_sku'] = $productChild->getSku();
        }
        $this->cart->addProduct($productChild, $paramsChild);
    }

    /**
     * Execute Bss_AjaxCart add grouped product with custom option.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $observer->getRequest()->getParams();
        $product = $observer->getProduct();
        $result = $observer->getResult();
        $messages = $result->getMessages();

        if (!isset($params['bss-gpo'])) {

            $typeInstance = $product->getTypeInstance();
            $associatedProducts = $typeInstance->getAssociatedProducts($product);
            $redirect = $this->returnCheckRedirect($associatedProducts);

            if ($redirect) {
                $result->setStatus(false);
                $messages[] = [
                    'type' => 'error',
                    'message' => __("Please specify product's required option(s).")
                ];
            }
        } else {
            try {
                foreach ($params['super_group'] as $id => $qty) {
                    // add product to cart
                    $this->addProductToCart($qty, $id, $params, $product, $result, $messages);
                }

                $result['added'] = true;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $result->setStatus(false);
                $messages[] = [
                    'type' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        $result->setMessages($messages);
        return $this;
    }
}
