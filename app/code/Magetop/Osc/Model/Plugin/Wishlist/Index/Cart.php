<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Osc
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Osc\Model\Plugin\Wishlist\Index;

use Magento\Checkout\Model\Cart as ModelCart;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Model\ItemFactory;
use Magetop\Osc\Helper\Data;

/**
 * Class Cart
 *
 * @package Magetop\Osc\Model\Plugin\Wishlist\Index
 */
class Cart
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ModelCart
     */
    private $cart;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * Cart constructor.
     *
     * @param Data         $helper
     * @param UrlInterface $url
     * @param ModelCart    $cart
     * @param ItemFactory  $itemFactory
     */
    public function __construct(
        Data $helper,
        UrlInterface $url,
        ModelCart $cart,
        ItemFactory $itemFactory
    ) {
        $this->helper      = $helper;
        $this->url         = $url;
        $this->cart        = $cart;
        $this->itemFactory = $itemFactory;
    }

    /**
     * @param \Magento\Wishlist\Controller\Index\Cart $subject
     * @param                                         $result
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterExecute(\Magento\Wishlist\Controller\Index\Cart $subject, $result)
    {
        if (!$this->helper->isEnabled()
            || !$this->helper->isRedirectToOneStepCheckout()
            || $this->cart->getQuote()->getHasError()
        ) {
            return $result;
        }

        $itemId = (int)$subject->getRequest()->getParam('item');
        /* @var $item \Magento\Wishlist\Model\Item */
        $item = $this->itemFactory->create()->load($itemId);
        if ($item->getId()) {
            return $result;
        }

        $redirectUrl = $this->url->getUrl($this->helper->getOscRoute());
        if ($subject->getRequest()->isAjax()) {
            $result->setData(['backUrl' => $redirectUrl]);
        } else {
            $result->setUrl($redirectUrl);
        }

        return $result;
    }
}
