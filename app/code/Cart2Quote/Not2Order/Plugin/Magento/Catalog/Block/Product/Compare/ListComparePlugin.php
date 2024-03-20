<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product\Compare;

use Magento\Catalog\Helper\Product\Compare;
use Magento\Catalog\Block\Product\Compare\ListCompare;
use Cart2Quote\Not2Order\Plugin\BasePlugin;
use Cart2Quote\Not2Order\Helper\Data;
use Cart2Quote\Not2Order\Html\Parser;

/**
 * Class ListComparePlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product\Compare
 */
class ListComparePlugin extends BasePlugin
{
    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    /**
     * ListComparePlugin constructor.
     * @param Parser $parser
     * @param Data $dataHelper
     * @param Compare $compareHelper
     */
    public function __construct(Parser $parser, Data $dataHelper, Compare $compareHelper)
    {
        $this->compareHelper = $compareHelper;
        parent::__construct($parser, $dataHelper);
    }

    /**
     * Remove add to cart button from compare list.
     *
     * @param ListCompare $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(ListCompare $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $this->parser->loadHtml($result);
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $containsString = $this->dataHelper->getContainsString();
        $replace = $this->dataHelper->replaceButtonCheck();
        $replaceButton = $this->dataHelper->getReplaceButton();

        foreach ($subject->getItems() as $item) {
            $addCartUrl = $this->compareHelper->getAddToCartUrl($item);
            $show = $this->dataHelper->hideOrderButton($item, $customerGroupId);

            $xpath = sprintf(
                '//form[@data-role="tocart-form"][@action="%s"]/button[%s]',
                $addCartUrl,
                $containsString
            );

            $domNodeList = $this->parser->xpath($xpath);
            foreach ($domNodeList as $domNode) {
                if (!$show && !$replace) {
                    $domNode->parentNode->removeChild($domNode);
                } elseif (!$show && $replace) {
                    $fragment = $this->parser->getDom()->createDocumentFragment();
                    $fragment->appendXML($replaceButton);
                    $domNode->parentNode->replaceChild($fragment, $domNode);
                }
            }
        }

        $result = $this->parser->getHtml();
        return $result;
    }
}
