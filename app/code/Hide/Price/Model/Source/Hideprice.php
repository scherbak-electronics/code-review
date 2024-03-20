<?php

namespace Hide\Price\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Hideprice extends AbstractSource
{
    public function getAllOptions()
    {
        $options = [
            ['value' => '', 'label' => __('--Select--')],
            ['value' => '0', 'label' => __('NO')],
            ['value' => '1', 'label' => __('YES')],
        ];

        return $options;
    }
}

