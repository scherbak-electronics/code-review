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

class Effect
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
                    ['value' => 'none', 'label' => __('None')],
                    ['value' => 'fade', 'label' => __('Fade')],
                    ['value' => 'elastic', 'label' => __('Elastic')]
                ];
        return $data;
    }
}
