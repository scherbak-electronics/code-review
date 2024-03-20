<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Desk\Block\Product\Tab;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Url;

/**
 * Class Success
 * @package Cart2Quote\Desk\Block\Product\Tab
 */
class Success extends \Cart2Quote\Desk\Block\Product\Tab\Form
{
    /**
     * Initialize success page
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_template = 'Cart2Quote_Desk::product/view/tab/success.phtml';
    }
}
