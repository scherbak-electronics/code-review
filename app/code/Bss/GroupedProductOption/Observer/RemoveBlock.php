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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveBlock implements ObserverInterface
{
    /**
     * @var \Bss\GroupedProductOption\Helper\Data
     */
    private $helperBss;

    /**
     * @param \Bss\GroupedProductOption\Helper\Data $helperBss
     */
    public function __construct(
        \Bss\GroupedProductOption\Helper\Data $helperBss
    ) {
        $this->helperBss = $helperBss;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        $block = $layout->getBlock('product.info.grouped');
        if ($block && $this->helperBss->getConfig()) {
            $layout->unsetElement('product.info.grouped');
        }
    }
}
