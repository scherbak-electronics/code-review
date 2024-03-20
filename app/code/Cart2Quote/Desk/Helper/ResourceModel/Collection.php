<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Helper\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Cart2Quote\Desk\Helper\ResourceModel
 */
class Collection extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Sets array(label => data, value => data)
     * to array(labelData, valueData)
     *
     * @param AbstractCollection $collection
     * @return array
     */
    public function toGridOptionArray(AbstractCollection $collection)
    {
        $options = $collection->toOptionArray();
        $newOptions = [];

        foreach ($options as $option) {
            $newOptions[$option['value']] = ucfirst($option['label']);
        }

        return $newOptions;
    }

    /**
     * Upper case the toOptionArray values
     *
     * @param AbstractCollection $collection
     * @return array
     */
    public function ucfirstToOptionArray(AbstractCollection $collection)
    {
        $options = $collection->toOptionArray();

        foreach ($options as $key => $option) {
            $option['label'] = ucfirst($option['label']);
            $options[$key] = $option;
        }

        return $options;
    }
}
