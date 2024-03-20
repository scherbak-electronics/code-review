<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Sales\Block\Reorder;

use Magento\Sales\Block\Reorder\Sidebar;
use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class SidebarPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Sales\Block\Reorder
 */
class SidebarPlugin extends BasePlugin
{
    /**
     * Remove add to cart from reorder sidebar.
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

        $this->parser->loadHtml($result);
        $containsString = $this->dataHelper->getContainsString();
        $xpath = sprintf('//button[%s]', $containsString);
        $domNodeList = $this->parser->xpath($xpath);

        if ($domNodeList->length > 0) {
            foreach ($domNodeList as $domNode) {
                $domNode->parentNode->removeChild($domNode);
            }
        }

        $result = $this->parser->getHtml();
        return $result;
    }
}
