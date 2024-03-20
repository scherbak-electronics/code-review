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

class Background
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
                    ['value' => 'dark', 'label' => __('Dark')],
                    ['value' => 'light', 'label' => __('Light')]
                ];
        return $data;
    }
}
