<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Helper;

use Cart2Quote\Not2Order\Model\Config\YesNoCustomerGroup;
use Cart2Quote\Not2Order\Model\Config\YesNoUseConfig;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\Session;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Cart2Quote\Not2Order\Helper
 */
class Data extends AbstractHelper
{
    /**
     * config path to enabled setting
     */
    const CONFIG_PATH_GENERAL_ENABLE = 'cart2quote_not2order/general/enable';

    /**
     * config path to hide order references setting
     */
    const CONFIG_PATH_HIDE_ORDER_REFERENCES = 'cart2quote_not2order/global/hidecart';

    /**
     * config path to hide order button setting
     */
    const CONFIG_PATH_HIDE_ORDER_BUTTON = 'cart2quote_not2order/global/hidebutton';

    /**
     * config path to hide order button groups setting
     */
    const CONFIG_PATH_HIDE_ORDER_BUTTON_GROUPS = 'cart2quote_not2order/global/hidebutton_groups';

    /**
     * config path to hide price setting
     */
    const CONFIG_PATH_HIDE_PRICE = 'cart2quote_not2order/global/hideprice';

    /**
     * config path to hide price groups setting
     */
    const CONFIG_PATH_HIDE_PRICE_GROUPS = 'cart2quote_not2order/global/hideprice_groups';

    /**
     * config path to hide order button if price is zero setting
     */
    const CONFIG_PATH_DISABLE_PRICE_ZERO = 'cart2quote_not2order/global/disablepricezero';

    /**
     * config path to replace order button setting
     */
    const CONFIG_PATH_REPLACE_ORDER_BUTTON = 'cart2quote_not2order/replacement_btn/enable';

    /**
     * config path to replace button values
     */
    const CONFIG_PATH_REPLACE_ORDER_BUTTON_VALUES = 'cart2quote_not2order/replacement_btn/';

    /**
     * config path to minicart div class
     */
    const CONFIG_PATH_MINI_CART_DIV_CLASS = 'cart2quote_not2order/advanced/minicartdivclass';

    /**
     * config path to minicart a class
     */
    const CONFIG_PATH_MINI_CART_A_CLASS = 'cart2quote_not2order/advanced/minicartaclass';

    /**
     * config path to add to cart button id name
     */
    const CONFIG_PATH_ADD_TO_CART_BTN_ID = 'cart2quote_not2order/advanced/addtocartid';

    /**
     * config path to add to cart button class name
     */
    const CONFIG_PATH_ADD_TO_CART_BTN_CLASS = 'cart2quote_not2order/advanced/addtocartclass';

    /**
     * config path to extra instant checkout div class name
     */
    const CONFIG_PATH_EXTRA_INSTANT_DIV_CLASS = 'cart2quote_not2order/advanced/instantcheckoutclass';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(Context $context, Session $customerSession, \Magento\Framework\Escaper $escaper)
    {
        $this->escaper = $escaper;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check product specific add to cart button visibility
     *
     * @return bool
     */
    public function getGlobalAddToCartConfig()
    {
        if ($this->isModuleOutputDisabled()) {
            return false;
        }

        return true;
    }

    /**
     * Check if Not2Order is enabled.
     *
     * @return bool
     */
    public function isModuleOutputDisabled()
    {
        $configEnabled = $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
        $isOutputEnabled = $this->_moduleManager->isOutputEnabled('Cart2Quote_Not2Order');

        return $configEnabled == Boolean::VALUE_NO || !$isOutputEnabled;
    }

    /**
     * Hide Order References
     *
     * @return bool
     */
    public function hideOrderReferences()
    {
        $configHideReferences = $this->scopeConfig->getValue(
            self::CONFIG_PATH_HIDE_ORDER_REFERENCES,
            ScopeInterface::SCOPE_STORE
        );

        return !$this->isModuleOutputDisabled() && $configHideReferences == Boolean::VALUE_YES;
    }

    /**
     * Hide Add To Cart button.
     *
     * @param Product $product
     * @param null $customerGroup
     * @return bool
     */
    public function hideOrderButton(Product $product, $customerGroup = null)
    {
        if (!$this->hideOrderReferences()) {
            $productButtonConfigValue = $product->getData('not2order_hide_orderbtn');

            $disablePriceZero = $this->getDisablePriceZero();
            if ($disablePriceZero && $product->getPrice() <= 0) {
                return false;
            }

            if ($productButtonConfigValue == YesNoUseConfig::VALUE_USECONFIG ||
                $productButtonConfigValue == null
            ) {
                $configHideOrderButton = $this->getHideOrderButtonConfigValue();
                if ($configHideOrderButton == YesNoCustomerGroup::VALUE_CUSTOMERGROUP) {
                    $configHideOrderButtonGroups = $this->getHideOrderButtonConfigValueGroups();
                    return !in_array($customerGroup, explode(',', $configHideOrderButtonGroups));
                }
                return $configHideOrderButton == YesNoCustomerGroup::VALUE_NO;
            } else {
                return $productButtonConfigValue == YesNoUseConfig::VALUE_NO;
            }
        }

        return false;
    }

    /**
     * Get show order button config.
     *
     * @param Product $product
     * @param null $customerGroup
     * @return bool
     */
    public function showButton(Product $product, $customerGroup = null)
    {
        if ($this->isModuleOutputDisabled()) {
            return true;
        }

        $productOrderButtonConfig = $product->getData('not2order_hide_orderbtn');

        if ($productOrderButtonConfig == YesNoUseConfig::VALUE_USECONFIG || $productOrderButtonConfig == null) {
            $configHideOrderBtn = $this->getHideOrderButtonConfigValue();
            if ($configHideOrderBtn == YesNoCustomerGroup::VALUE_CUSTOMERGROUP) {
                $configHideOrderBtnGroups = $this->getHideOrderButtonConfigValueGroups();
                return !in_array($customerGroup, explode(',', $configHideOrderBtnGroups));
            }

            return $configHideOrderBtn == YesNoCustomerGroup::VALUE_NO;
        } else {
            return $productOrderButtonConfig == YesNoUseConfig::VALUE_NO;
        }
    }

    /**
     * Hide Price.
     *
     * @param Product $product
     * @param null $customerGroup
     * @return bool
     */
    public function showPrice(Product $product, $customerGroup = null)
    {
        if ($this->isModuleOutputDisabled()) {
            return true;
        }

        $productPriceConfigValue = $product->getData('not2order_hide_price');

        if ($productPriceConfigValue == null) {
            $productPriceConfigValue = $product->getData('not2order_hide_price');
        }

        if ($productPriceConfigValue == YesNoUseConfig::VALUE_USECONFIG || $productPriceConfigValue == null) {
            $configHidePrice = $this->getHidePrice();
            if ($configHidePrice == YesNoCustomerGroup::VALUE_CUSTOMERGROUP) {
                $configHidePriceGroups = $this->getHidePriceGroups();
                return !in_array($customerGroup, explode(',', $configHidePriceGroups));
            }

            return $configHidePrice == YesNoCustomerGroup::VALUE_NO;
        } else {
            return $productPriceConfigValue == YesNoUseConfig::VALUE_NO;
        }
    }

    /**
     * Get hide order button setting.
     *
     * @return mixed
     */
    public function getHideOrderButtonConfigValue()
    {
        $configHideOrderButton = $this->scopeConfig->getValue(
            self::CONFIG_PATH_HIDE_ORDER_BUTTON,
            ScopeInterface::SCOPE_STORE
        );

        return $configHideOrderButton;
    }

    /**
     * Get hide order button group setting
     *
     * @return mixed
     */
    public function getHideOrderButtonConfigValueGroups()
    {
        $configHideOrderButtonGroups = $this->scopeConfig->getValue(
            self::CONFIG_PATH_HIDE_ORDER_BUTTON_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        return $configHideOrderButtonGroups;
    }

    /**
     * Get hide price setting.
     *
     * @return mixed
     */
    public function getHidePrice()
    {
        $configHidePrice = $this->scopeConfig->getValue(
            self::CONFIG_PATH_HIDE_PRICE,
            ScopeInterface::SCOPE_STORE
        );

        return $configHidePrice;
    }

    /**
     * Get hide price group setting.
     *
     * @return mixed
     */
    public function getHidePriceGroups()
    {
        $configHidePriceGroups = $this->scopeConfig->getValue(
            self::CONFIG_PATH_HIDE_PRICE_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        return $configHidePriceGroups;
    }

    /**
     * Get disable price zero setting.
     *
     * @return bool
     */
    public function getDisablePriceZero()
    {
        $configHidePrice = $this->scopeConfig->getValue(
            self::CONFIG_PATH_DISABLE_PRICE_ZERO,
            ScopeInterface::SCOPE_STORE
        );

        return $configHidePrice;
    }

    /**
     * Checks if has to replace or hide the order button.
     *
     * @return boolean
     */
    public function replaceButtonCheck()
    {
         return $this->scopeConfig->getValue(
             self::CONFIG_PATH_REPLACE_ORDER_BUTTON,
             ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * Get replacement button values.
     *
     * Returns the replacement button
     * @return replacement button
     */
    public function getReplacementButtonData()
    {
        $button = [
            "title" => "Random button",
            "url" => "/",
            "classes" => "action primary tocart additional-button",
            "target" => "_self"
        ];

        foreach ($button as $key => $value) {
            $configValue = $this->scopeConfig->getValue(
                self::CONFIG_PATH_REPLACE_ORDER_BUTTON_VALUES . $key,
                ScopeInterface::SCOPE_STORE
            );

            if ($configValue && !empty($configValue)) {
                $button[$key] = $configValue;
            }
        }

        return $button;
    }

    /**
     * Format replacement button layout to string.
     *
     * @return string
     */
    public function getReplaceButton()
    {
        $replaceButton = $this->getReplacementButtonData();

        $html = sprintf(
            '<a href="%s" title="%s" target="%s" class="%s"><span>%s</span></a>',
            $this->escaper->escapeUrl($replaceButton["url"]),
            $replaceButton["title"],
            $replaceButton["target"],
            $replaceButton["classes"],
            $replaceButton["title"]
        );

        return $html;
    }

    /**
     * Get minicart div class.
     *
     * @return mixed
     */
    public function getMiniCartDivClass()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_MINI_CART_DIV_CLASS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get minicart a class.
     *
     * @return mixed
     */
    public function getMiniCartAClass()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_MINI_CART_A_CLASS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Add To Cart button id.
     *
     * @return mixed
     */
    public function getAddToCartId()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ADD_TO_CART_BTN_ID, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Add To Cart button class.
     *
     * @return mixed
     */
    public function getAddToCartClass()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ADD_TO_CART_BTN_CLASS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get extra instant checkout div class.
     *
     * @return mixed
     */
    public function getExtraInstantCheckoutDivClass()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_EXTRA_INSTANT_DIV_CLASS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get customer group id.
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        return $customerGroupId;
    }

    /**
     * This method provides the 'contains string' for the xpath in several templates
     * The xpath is then used to determine the location of the add-to-cart button
     *
     * @param array $notContaining
     * @return string
     */
    public function getContainsString($notContaining = ['toquote'])
    {
        $buttonClass = $this->getAddToCartClass();
        $buttonClassArray = explode(" ", $buttonClass);
        $containsString = "";

        foreach ($buttonClassArray as $key => $class) {
            if (!empty($containsString)) {
                $containsString .= " and ";
            }
            $containsString .= sprintf('contains(@class, "%s")', $class);
        }

        foreach ($notContaining as $class) {
            if (!empty($containsString)) {
                $containsString .= " and ";
            }
            $containsString .= sprintf('not(contains(@class, "%s"))', $class);
        }

        //$containsString example: 'contains(@class, "action") and contains(@class, "tocart")'
        return $containsString;
    }

    /**
     * @param Product $product
     * @param int $customerGroupId
     * @return bool
     */
    public function isQuotable(Product $product, $customerGroupId)
    {
        $quotable = $product->getData('cart2quote_quotable');
        if (isset($quotable)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            return $objectManager->create('Cart2Quote\Quotation\Helper\Data')->isQuotable($product, $customerGroupId);
        }

        return false;
    }
}
