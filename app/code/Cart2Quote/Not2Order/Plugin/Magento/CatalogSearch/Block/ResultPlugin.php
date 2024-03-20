<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Not2Order\Plugin\Magento\CatalogSearch\Block;

use Magento\CatalogSearch\Block\Result;
use Cart2Quote\Not2Order\Plugin\BasePlugin;

/**
 * Class ViewPlugin
 * @package Cart2Quote\Not2Order\Plugin\Magento\Catalog\Block\Product
 */
class ResultPlugin extends BasePlugin
{
    /**
     * @param \Magento\CatalogSearch\Block\Result $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(Result $subject, $result)
    {
        if ($this->dataHelper->isModuleOutputDisabled()) {
            return $result;
        }

        $this->parser->loadHtml($result);
        $containsString = $this->dataHelper->getContainsString();
        $xpath = sprintf(
            '//form[@data-role="tocart-form"]/input[@type="hidden"][@name="product"]/parent::form/button[%s]',
            $containsString
        );

        $domNodeList = $this->parser->xpath($xpath);
        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $replace = $this->dataHelper->replaceButtonCheck();
        $replaceButton = $this->dataHelper->getReplaceButton();

        foreach ($domNodeList as $domNode) {
            $form = $domNode->parentNode;

            foreach ($form->childNodes as $childNode) {
                if ($childNode->nodeName == 'input' && $childNode->getAttribute('type') == 'hidden' &&
                    $childNode->getAttribute('name') == 'product') {
                    $value = $childNode->getAttribute('value');
                    $actualProduct = $subject->getListBlock()->getLoadedProductCollection()->getItemById($value);
                    $show = $this->dataHelper->hideOrderButton($actualProduct, $customerGroupId);
                    if (!$show && !$replace) {
                        $domNode->parentNode->removeChild($domNode);
                    } elseif (!$show && $replace) {
                        $fragment = $this->parser->getDom()->createDocumentFragment();
                        $fragment->appendXML($replaceButton);
                        $domNode->parentNode->replaceChild($fragment, $domNode);
                    }
                }
            }
        }

        $result = $this->parser->getHtml();
        return $result;
    }
}
