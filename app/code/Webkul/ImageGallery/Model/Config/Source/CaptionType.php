<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_ImageGallery
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\ImageGallery\Model\Config\Source;

class CaptionType
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
                    ['value' => 'float', 'label' => __('Float')],
                    ['value' => 'inside', 'label' => __('Inside')],
                    ['value' => 'outside', 'label' => __('Outside')],
                    ['value' => 'over', 'label' => __('Over')]
                ];
        return $data;
    }
}
