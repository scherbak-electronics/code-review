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
namespace Bss\GroupedProductOption\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Checkout\Model\Cart as CustomerCart;

class Add extends \Cart2Quote\Quotation\Controller\Quote\Add
{
    /**
     * Bss grouped product option helper.
     *
     * @var \Bss\GroupedProductOption\Helper\Data
     */
    private $helperBss;

    /**
     * Product repository.
     *
     * @var ProductRepositoryInterface
     */
    public $productRepository;

        protected $_checkoutSession;
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param \Bss\GroupedProductOption\Helper\Data $helperBss
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider $strategyProvider,
        \Magento\Customer\Model\Session $customerSession,
        \Bss\GroupedProductOption\Helper\Data $helperBss,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory,
            $productRepository,
            $productHelper,
            $quotationDataHelper,
            $resolverInterface,
            $escaper,
            $logger,
            $jsonHelper,
            $strategyProvider,
            $customerSession
        );
        $this->_checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->helperBss = $helperBss;
    }

    /**
     * @param int $qty
     * @param int $id
     * @param array $params
     * @param mixed $product
     * @return bool|Add|\Magento\Framework\Controller\Result\Redirect
     */
    protected function addProductToCart($qty, $id, $params, $product)
    {
        if ($this->checkQty($qty)) {
            return true;
        }
        //$params['uenc'] = 'aHR0cHM6Ly9sdW1lbnN0YXJsZWQud2VidmFuY291dmVyZGVzaWduLmNvbS9xdW90YXRpb24vcXVvdGUv';
        $productChild = $this->productRepository->getById($id);
        $paramsChild = [];
        // $paramsChild['uenc'] = $params['uenc'];
        $paramsChild['uenc'] = '';
        $paramsChild['product'] = $id;
        $paramsChild['selected_configurable_option'] = $params['selected_configurable_option'];
        if (isset($params['options_' . $id]) && !empty($params['options_' . $id]) > 0) {
            $paramsChild['options'] = $params['options_' . $id];
        }

        if (isset($params['option_qty']) && isset($paramsChild['options'])) {
            foreach ($paramsChild['options'] as $optionId => $value) {
                $paramsChild['option_qty'][$optionId] = $params['option_qty'][$optionId] ?? 1;
            }
        }

        if ($this->checkParamGpoOption($params, $id)) {
            foreach ($params['bss-gpo-option-' . $id] as $name => $value) {
                $paramsChild[$name] = $value;

                preg_match('/options_([0-9]*)_file_action/', $name, $matchedOptionId);
                if (isset($matchedOptionId[1])) {
                    $paramsChild['option_qty'][$matchedOptionId[1]] = $params['option_qty'][$matchedOptionId[1]] ?? 1;
                }
            }
        }

        $paramsChild['qty'] = $qty;
        if (isset($paramsChild['qty'])) {
            $this->helperBss->returnLocalizedToNormalized()->setOptions(
                ['locale' => $this->helperBss->returnResolver()->getLocale()]
            );
            $paramsChild['qty'] = $this->helperBss->returnLocalizedToNormalized()->filter($paramsChild['qty']);
        }

        if ($productChild->getTypeId() == 'configurable') {
            $paramsChild['super_attribute'] = $params['super_attribute'][$id];
        } else {
            $paramsChild['super_product_config'] = [
                'product_type' => $product->getTypeId(),
                'product_id' => $params['product']
            ];
        }

        if ($productChild->getData('visibility') == Visibility::VISIBILITY_NOT_VISIBLE) {
            $paramsChild['super_product_config'] = [
                'product_type' => $product->getTypeId(),
                'product_id' => $params['product']
            ];
        } else {
            $paramsChild['super_product_config'] = [
                'product_type' => $productChild->getTypeId(),
                'product_id' => $params['product']
            ];
        }
        /**
         * Check product availability
         */
        if (!$productChild) {
            return $this->goBack();
        }
        if ($productChild->getTypeId() == 'grouped') {
            $paramsChild['simple_name'] = $productChild->getname();
            $paramsChild['simple_sku'] = $productChild->getSku();
        }
        $this->helperBss->returnRegistry()->unregister('bss-gpo-group-add');
        $this->helperBss->returnRegistry()->register('bss-gpo-group-add', $id);
        $this->cart->addProduct($productChild, $paramsChild);
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
    private function checkParamGpoOption($params, $id)
    {
        if (isset($params['bss-gpo-option-' . $id]) && !empty($params['bss-gpo-option-' . $id]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param mixed $e
     */
    private function displayMessageError($e)
    {
        if ($this->_checkoutSession->getUseNotice(true)) {
            $this->messageManager->addNoticeMessage(
                $this->helperBss->returnEscaper()->escapeHtml($e->getMessage())
            );
        } else {
            if (!empty($e->getMessage())) {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->helperBss->returnEscaper()->escapeHtml($message)
                    );
                }
            }
        }
    }

    /**
     * @param mixed $product
     */
    private function displaySuccessMessage($product)
    {
        if (!$this->cart->getQuote()->getHasError()) {
            $message = __(
                'You added %1 to your shopping cart.',
                $product->getName()
            );
            $this->messageManager->addSuccessMessage($message);
        }
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

    /***
     * @param mixed $product
     * @return bool
     */
    private function returnCheckType($product)
    {
        if (!$this->helperBss->getConfig() || $product->getTypeId() !=
            \Bss\GroupedProductOption\Helper\Data::PRODUCT_TYPE_GROUPED) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    private function returnRedirectUrl()
    {
        $url = $this->_checkoutSession->getRedirectUrl(true);

        if (!$url) {
            $cartUrl = $this->helperBss->returnCartHelper()->getCartUrl();
            $url = $this->_redirect->getRedirectUrl($cartUrl);
        }
        return $url;
    }

    /**
     * @param array $related
     */
    private function returnAddProductsByIds($related)
    {
        if (!empty($related)) {
            $this->cart->addProductsByIds(explode(',', $related));
        }
    }

    /**
     * @param null $coreRoute
     * @return Add|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute($coreRoute = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $logger = $objectManager->get('\Lumenstarled\Override\Helper\Logger');
            
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();

        $product = $this->_initProduct();
        if ($this->returnCheckType($product)) {
            return parent::execute($coreRoute);
        }

        // check in category
        if (!isset($params['bss-gpo'])) {
            $typeInstance = $product->getTypeInstance();
            $associatedProducts = $typeInstance->getAssociatedProducts($product);
            $redirect = $this->returnCheckRedirect($associatedProducts);

            if ($redirect) {
                $url = $product->getUrlModel()->getUrl($product);
                $this->messageManager->addNoticeMessage(__("Please specify product's required option(s)."));
                return $this->goBack($url);
            } else {
                return parent::execute($coreRoute);
            }
        } else {
            // add to cart function
            $this->helperBss->returnRegistry()->register('bss-gpo-group', $params['product']);
            try {
                foreach ($params['super_group'] as $id => $qty) {
                    $this->addProductToCart($qty, $id, $params, $product);
                }

                $related = $this->getRequest()->getParam('related_product');
                // add product by id
                $this->returnAddProductsByIds($related);

                $this->cart->save();

                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );

                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    // display success message
                    $this->displaySuccessMessage($product);
                    return $this->goBack(null, $product);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // Display message error
                $this->displayMessageError($e);

                $url = $this->returnRedirectUrl();

                return $this->goBack($url);
            } catch (\Exception $e) {
                $this->messageManager
                    ->addExceptionMessage($e, __('We can\'t add this item to your shopping cart right now.'));
                $this->helperBss->returnLogger()->critical($e);
                return $this->goBack();
            }
        }
    }
}
