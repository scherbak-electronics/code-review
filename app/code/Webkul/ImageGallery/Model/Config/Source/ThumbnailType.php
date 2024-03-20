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

class ThumbnailType
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data =  [
                    ['value' => 0, 'label' => __('Very Small')],
                    ['value' => 1, 'label' => __('Small')],
                    ['value' => 2, 'label' => __('Medium')],
                    ['value' => 3, 'label' => __('Large')],
                    ['value' => 4, 'label' => __('Very Large')]
                ];
        return $data;
    }
}
