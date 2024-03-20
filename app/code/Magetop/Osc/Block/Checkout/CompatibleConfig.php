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

namespace Magetop\Osc\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magetop\Osc\Helper\Data as OscHelper;

/**
 * Class CompatibleConfig
 * @package Magetop\Osc\Block\Checkout
 */
class CompatibleConfig extends Template
{
    /**
     * @var string $_template
     */
    protected $_template = 'Magetop_Osc::onepage/compatible-config.phtml';

    /**
     * @var OscHelper
     */
    protected $_oscHelper;

    /**
     * CompatibleConfig constructor.
     *
     * @param Template\Context $context
     * @param OscHelper $oscHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        OscHelper $oscHelper,
        array $data = []
    ) {
        $this->_oscHelper = $oscHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnableModulePostNL()
    {
        return $this->_oscHelper->isEnableModulePostNL();
    }
}
