<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Checkout\Block\Cart;

use Magento\Checkout\Block\Cart\Sidebar;
use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class SidebarPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Checkout\Block\Cart
 */
class SidebarPlugin extends BasePlugin
{
    /**
     * Remove Minicart
     *
     * @param Sidebar $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(Sidebar $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        if ($this->dataHelper->hideOrderReferences()) {
            $this->parser->loadHtml($result);
            $divClassName = $this->dataHelper->getMiniCartDivClass();
            $aClassName = $this->dataHelper->getMiniCartAClass();
            $xpath = sprintf('//div[@class="%s"]/a[@class="%s"]', $divClassName, $aClassName);

            $domNodeList = $this->parser->xpath($xpath);
            if ($domNodeList->length > 0) {
                foreach ($domNodeList as $domNode) {
                    $domNode->parentNode->removeChild($domNode);
                }
            }

            $result = $this->parser->getHtml();
        }

        return $result;
    }
}
