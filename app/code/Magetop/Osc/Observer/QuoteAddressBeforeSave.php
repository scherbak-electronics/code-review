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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QuoteAddressBeforeSave
 * @package Magetop\Osc\Observer
 */
class QuoteAddressBeforeSave implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $dataObject = $observer->getEvent()->getDataObject();
        for ($i = 1; $i <= 3; $i++) {
            $attr = 'mposc_field_' . $i;
            if ($oscFieldData = $dataObject->getData($attr)) {
                if ($oscFieldData === $attr) {
                    $dataObject->setData($attr, null);
                } elseif (strpos($oscFieldData, "\n") !== false) {
                    $dataObject->setData($attr, explode("\n", $oscFieldData)[1]);
                }
            }
        }
    }
}
