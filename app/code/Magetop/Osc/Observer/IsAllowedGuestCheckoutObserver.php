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

namespace Magetop\Osc\Observer;

use Magento\Downloadable\Observer\IsAllowedGuestCheckoutObserver as DownloadableAllowedGuestCheckoutObserver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magetop\Osc\Helper\Data;

/**
 * Class IsAllowedGuestCheckoutObserver
 * @package Magetop\Osc\Observer
 */
class IsAllowedGuestCheckoutObserver extends DownloadableAllowedGuestCheckoutObserver implements ObserverInterface
{
    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $helper = ObjectManager::getInstance()->get(Data::class);
        if ($helper->isEnabled()) {
            return $this;
        }

        return parent::execute($observer);
    }
}
