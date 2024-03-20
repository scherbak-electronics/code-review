<?php
/**
 * Elavon Model AbstractMethod.
 *
 * @category  Payment Integration
 * @package   Rootways_Elavon
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/shop/media/extension_doc/license_agreement.pdf
 */
namespace Rootways\Elavon\Model\Source;

use Magento\Payment\Model\Method\AbstractMethod;

class YesNo implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Yes (Recommended)'),
            ],
            [
                'value' => 0,
                'label' => __('No'),
            ],
        ];
    }
}
