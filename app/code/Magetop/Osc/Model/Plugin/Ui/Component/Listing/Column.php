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

namespace Magetop\Osc\Model\Plugin\Ui\Component\Listing;

/**
 * Class Column
 * @package Magetop\Osc\Model\Plugin\Ui\Component\Listing
 */
class Column
{
    public function afterPrepare(\Magento\Ui\Component\Listing\Columns\Column $subject, $result)
    {
        if ($subject->getName() === 'billing_mposc_field_3') {
            $config = $subject->getData('config');
            unset($config['timezone']);
            $subject->setData('config', $config);
        }

        return $result;
    }
}
